<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorContentActivity.php                                                |
// |                                                                           |
// | Lightweight content lifecycle observation journal.                       |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorcontentactivity.php') !== false) {
    die('This file can not be used on its own.');
}

/**
 * Return the per-site activity journal path below path_data.
 *
 * @return string
 */
function MONITOR_ACTIVITY_path()
{
    global $_CONF;

    if (empty($_CONF['path_data']) || !is_dir($_CONF['path_data'])) {
        return '';
    }

    $site = isset($_CONF['site_url']) ? (string) $_CONF['site_url'] : '';
    $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
    $suffix = sha1($site . '|' . $host);

    return rtrim($_CONF['path_data'], '/\\')
        . '/monitor-content-activity-' . $suffix . '.json';
}

/**
 * Normalize retention for a journal event list.
 *
 * @param array $events
 * @return array
 */
function MONITOR_ACTIVITY_prune($events)
{
    $cutoff = time() - (30 * 86400);
    $kept = array();

    foreach ($events as $event) {
        if (!is_array($event)) {
            continue;
        }
        $timestamp = isset($event['timestamp']) ? (int) $event['timestamp'] : 0;
        if ($timestamp >= $cutoff) {
            $kept[] = $event;
        }
    }

    if (count($kept) > 500) {
        $kept = array_slice($kept, -500);
    }

    return array_values($kept);
}

/**
 * Read the current journal.
 *
 * @return array
 */
function MONITOR_ACTIVITY_read()
{
    $path = MONITOR_ACTIVITY_path();
    if ($path === '' || !is_file($path) || !is_readable($path)) {
        return array();
    }

    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return array();
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded) || !isset($decoded['events']) || !is_array($decoded['events'])) {
        return array();
    }

    return MONITOR_ACTIVITY_prune($decoded['events']);
}

/**
 * Append one lifecycle observation under one exclusive read/modify/write lock.
 *
 * Retention is deliberately both time- and count-bounded: 30 days / 500 events.
 * The journal contains only object identity and lifecycle metadata, never content.
 *
 * @param string $event saved|deleted
 * @param string $id
 * @param string $type
 * @param string $subType
 * @param string $oldId
 * @return bool
 */
function MONITOR_ACTIVITY_record($event, $id, $type, $subType, $oldId)
{
    $event = (string) $event;
    if (!in_array($event, array('saved', 'deleted'), true)) {
        return false;
    }

    $id = trim((string) $id);
    $type = trim((string) $type);
    $subType = trim((string) $subType);
    $oldId = trim((string) $oldId);

    if ($id === '' || $type === '') {
        return false;
    }

    /* Monitor observes other content owners; it does not journal itself. */
    if (strcasecmp($type, 'monitor') === 0) {
        return true;
    }

    $path = MONITOR_ACTIVITY_path();
    if ($path === '' || !is_writable(dirname($path))) {
        return false;
    }

    $handle = @fopen($path, 'c+');
    if ($handle === false || !@flock($handle, LOCK_EX)) {
        if (is_resource($handle)) {
            @fclose($handle);
        }
        return false;
    }

    @rewind($handle);
    $raw = stream_get_contents($handle);
    $events = array();
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && isset($decoded['events']) && is_array($decoded['events'])) {
            $events = $decoded['events'];
        }
    }

    $events[] = array(
        'timestamp' => time(),
        'event' => $event,
        'type' => $type,
        'id' => $id,
        'sub_type' => $subType,
        'old_id' => ($event === 'saved' && $oldId !== '' && $oldId !== $id) ? $oldId : ''
    );
    $events = MONITOR_ACTIVITY_prune($events);

    $json = json_encode(array(
        'format' => 1,
        'events' => $events
    ));

    $written = false;
    if (is_string($json) && $json !== '') {
        @rewind($handle);
        if (@ftruncate($handle, 0)) {
            $bytes = @fwrite($handle, $json);
            if ($bytes !== false && $bytes === strlen($json)) {
                @fflush($handle);
                $written = true;
            }
        }
    }

    @flock($handle, LOCK_UN);
    @fclose($handle);

    return $written;
}

/**
 * Return activity inside a time range, newest first.
 *
 * @param int $from Inclusive unix timestamp; 0 means no lower bound.
 * @param int $to   Inclusive unix timestamp; 0 means no upper bound.
 * @param int $limit
 * @return array
 */
function MONITOR_ACTIVITY_between($from, $to, $limit)
{
    $from = max(0, (int) $from);
    $to = max(0, (int) $to);
    $limit = (int) $limit;
    if ($limit < 1) {
        $limit = 1;
    } elseif ($limit > 500) {
        $limit = 500;
    }

    $matched = array();
    foreach (MONITOR_ACTIVITY_read() as $event) {
        $timestamp = isset($event['timestamp']) ? (int) $event['timestamp'] : 0;
        if ($timestamp <= 0) {
            continue;
        }
        if ($from > 0 && $timestamp < $from) {
            continue;
        }
        if ($to > 0 && $timestamp > $to) {
            continue;
        }
        $matched[] = $event;
    }

    usort($matched, function ($a, $b) {
        $left = isset($a['timestamp']) ? (int) $a['timestamp'] : 0;
        $right = isset($b['timestamp']) ? (int) $b['timestamp'] : 0;
        if ($left === $right) {
            return 0;
        }

        return ($left > $right) ? -1 : 1;
    });

    return array_slice($matched, 0, $limit);
}

/**
 * Summarize observed content lifecycle activity.
 *
 * @param int $from
 * @param int $to
 * @param int $limit
 * @return array
 */
function MONITOR_ACTIVITY_summary($from, $to, $limit)
{
    $items = MONITOR_ACTIVITY_between($from, $to, $limit);
    $saved = 0;
    $deleted = 0;

    foreach ($items as $item) {
        if (isset($item['event']) && $item['event'] === 'deleted') {
            $deleted++;
        } else {
            $saved++;
        }
    }

    return array(
        'saved' => $saved,
        'deleted' => $deleted,
        'count' => count($items),
        'items' => $items,
        'retention_days' => 30,
        'max_events' => 500
    );
}
