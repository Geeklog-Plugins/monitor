<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | lib/MonitorChanges.php                                                    |
// |                                                                           |
// | Lightweight per-site snapshots and change detection.                     |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorchanges.php') !== false) {
    die();
}

function MONITOR_CHANGES_snapshotPath()
{
    global $_CONF;

    if (empty($_CONF['path_data']) || !is_dir($_CONF['path_data'])) {
        return '';
    }

    $site = isset($_CONF['site_url']) ? (string) $_CONF['site_url'] : '';
    $host = isset($_SERVER['HTTP_HOST']) ? (string) $_SERVER['HTTP_HOST'] : '';
    $suffix = sha1($site . '|' . $host);

    return rtrim($_CONF['path_data'], '/\\')
        . '/monitor-changes-' . $suffix . '.json';
}

function MONITOR_CHANGES_geeklogVersion()
{
    global $_CONF;

    if (defined('VERSION')) {
        return (string) VERSION;
    }
    if (isset($_CONF['version'])) {
        return (string) $_CONF['version'];
    }

    return '';
}

function MONITOR_CHANGES_collectPlugins()
{
    global $_TABLES;

    $plugins = array();
    $result = DB_query(
        "SELECT pi_name, pi_version, pi_enabled FROM {$_TABLES['plugins']} ORDER BY pi_name",
        1
    );

    if (!$result) {
        return $plugins;
    }

    while ($row = DB_fetchArray($result)) {
        if (empty($row['pi_name'])) {
            continue;
        }

        $plugins[(string) $row['pi_name']] = array(
            'version' => isset($row['pi_version']) ? (string) $row['pi_version'] : '',
            'enabled' => !empty($row['pi_enabled'])
        );
    }

    return $plugins;
}

function MONITOR_CHANGES_collectSnapshot($source)
{
    global $_CONF;

    $errorPath = isset($_CONF['path_log'])
        ? rtrim($_CONF['path_log'], '/\\') . '/error.log'
        : '';
    $errorSize = ($errorPath !== '' && is_file($errorPath)) ? @filesize($errorPath) : false;
    $errorMtime = ($errorPath !== '' && is_file($errorPath)) ? @filemtime($errorPath) : false;

    $diskPath = isset($_CONF['path']) ? $_CONF['path'] : '';
    $diskFree = ($diskPath !== '' && is_dir($diskPath)) ? @disk_free_space($diskPath) : false;
    $diskTotal = ($diskPath !== '' && is_dir($diskPath)) ? @disk_total_space($diskPath) : false;

    return array(
        'timestamp' => time(),
        'source' => (string) $source,
        'environment' => array(
            'geeklog' => MONITOR_CHANGES_geeklogVersion(),
            'php' => PHP_VERSION
        ),
        'plugins' => MONITOR_CHANGES_collectPlugins(),
        'disk' => array(
            'free' => ($diskFree === false) ? null : (float) $diskFree,
            'total' => ($diskTotal === false) ? null : (float) $diskTotal
        ),
        'error_log' => array(
            'path' => $errorPath,
            'size' => ($errorSize === false) ? null : (int) $errorSize,
            'mtime' => ($errorMtime === false) ? null : (int) $errorMtime
        )
    );
}

function MONITOR_CHANGES_readHistory()
{
    $path = MONITOR_CHANGES_snapshotPath();
    if ($path === '' || !is_file($path) || !is_readable($path)) {
        return array();
    }

    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return array();
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return array();
    }

    return isset($decoded['snapshots']) && is_array($decoded['snapshots'])
        ? $decoded['snapshots']
        : array();
}

function MONITOR_CHANGES_writeHistory($snapshots)
{
    $path = MONITOR_CHANGES_snapshotPath();
    if ($path === '' || !is_array($snapshots)) {
        return false;
    }

    $directory = dirname($path);
    if (!is_writable($directory)) {
        return false;
    }

    if (count($snapshots) > 10) {
        $snapshots = array_slice($snapshots, -10);
    }

    $json = json_encode(array(
        'format' => 1,
        'snapshots' => array_values($snapshots)
    ));
    if (!is_string($json) || $json === '') {
        return false;
    }

    return @file_put_contents($path, $json, LOCK_EX) !== false;
}

function MONITOR_CHANGES_capture($source)
{
    $history = MONITOR_CHANGES_readHistory();
    $history[] = MONITOR_CHANGES_collectSnapshot($source);

    return MONITOR_CHANGES_writeHistory($history);
}

function MONITOR_CHANGES_compare($previous, $current)
{
    $changes = array();

    $previousEnvironment = isset($previous['environment']) && is_array($previous['environment'])
        ? $previous['environment'] : array();
    $currentEnvironment = isset($current['environment']) && is_array($current['environment'])
        ? $current['environment'] : array();

    foreach (array('geeklog', 'php') as $key) {
        $before = isset($previousEnvironment[$key]) ? (string) $previousEnvironment[$key] : '';
        $after = isset($currentEnvironment[$key]) ? (string) $currentEnvironment[$key] : '';
        if ($before !== $after) {
            $changes[] = array(
                'type' => 'environment',
                'code' => $key . '_changed',
                'item' => $key,
                'before' => $before,
                'after' => $after,
                'severity' => 'info'
            );
        }
    }

    $previousPlugins = isset($previous['plugins']) && is_array($previous['plugins'])
        ? $previous['plugins'] : array();
    $currentPlugins = isset($current['plugins']) && is_array($current['plugins'])
        ? $current['plugins'] : array();
    $names = array_unique(array_merge(array_keys($previousPlugins), array_keys($currentPlugins)));
    natcasesort($names);

    foreach ($names as $name) {
        $had = isset($previousPlugins[$name]);
        $has = isset($currentPlugins[$name]);

        if (!$had && $has) {
            $changes[] = array(
                'type' => 'plugin',
                'code' => 'plugin_installed',
                'item' => $name,
                'before' => '',
                'after' => isset($currentPlugins[$name]['version']) ? $currentPlugins[$name]['version'] : '',
                'severity' => 'info'
            );
            continue;
        }
        if ($had && !$has) {
            $changes[] = array(
                'type' => 'plugin',
                'code' => 'plugin_removed',
                'item' => $name,
                'before' => isset($previousPlugins[$name]['version']) ? $previousPlugins[$name]['version'] : '',
                'after' => '',
                'severity' => 'review'
            );
            continue;
        }

        $beforeVersion = isset($previousPlugins[$name]['version']) ? (string) $previousPlugins[$name]['version'] : '';
        $afterVersion = isset($currentPlugins[$name]['version']) ? (string) $currentPlugins[$name]['version'] : '';
        if ($beforeVersion !== $afterVersion) {
            $changes[] = array(
                'type' => 'plugin',
                'code' => 'plugin_version_changed',
                'item' => $name,
                'before' => $beforeVersion,
                'after' => $afterVersion,
                'severity' => 'info'
            );
        }

        $beforeEnabled = !empty($previousPlugins[$name]['enabled']);
        $afterEnabled = !empty($currentPlugins[$name]['enabled']);
        if ($beforeEnabled !== $afterEnabled) {
            $changes[] = array(
                'type' => 'plugin',
                'code' => $afterEnabled ? 'plugin_enabled' : 'plugin_disabled',
                'item' => $name,
                'before' => $beforeEnabled,
                'after' => $afterEnabled,
                'severity' => 'info'
            );
        }
    }

    $previousDisk = isset($previous['disk']) && is_array($previous['disk']) ? $previous['disk'] : array();
    $currentDisk = isset($current['disk']) && is_array($current['disk']) ? $current['disk'] : array();
    $beforeFree = isset($previousDisk['free']) ? $previousDisk['free'] : null;
    $afterFree = isset($currentDisk['free']) ? $currentDisk['free'] : null;
    $total = isset($currentDisk['total']) ? $currentDisk['total'] : null;

    if ($beforeFree !== null && $afterFree !== null && $total && $beforeFree > $afterFree) {
        $drop = $beforeFree - $afterFree;
        if ($drop >= 104857600 || ($drop / $total) >= 0.02) {
            $changes[] = array(
                'type' => 'disk',
                'code' => 'disk_free_decreased',
                'item' => 'disk',
                'before' => (float) $beforeFree,
                'after' => (float) $afterFree,
                'severity' => (($afterFree / $total) < 0.10) ? 'warning' : 'info'
            );
        }
    }

    return $changes;
}

function MONITOR_CHANGES_normalizeLogLine($line)
{
    $line = trim((string) $line);
    if ($line === '') {
        return '';
    }

    $line = preg_replace('/^\[[^\]]+\]\s*/', '', $line);
    $line = preg_replace('/\bline\s+\d+\b/i', 'line #', $line);
    $line = preg_replace('/0x[0-9a-f]+/i', '0x#', $line);
    $line = preg_replace('/\s+/', ' ', $line);

    if (strlen($line) > 240) {
        $line = substr($line, 0, 237) . '...';
    }

    return $line;
}

function MONITOR_CHANGES_errorDelta($previous, $current)
{
    $result = array(
        'available' => false,
        'rotated' => false,
        'truncated' => false,
        'bytes' => 0,
        'signatures' => array()
    );

    $before = isset($previous['error_log']) && is_array($previous['error_log'])
        ? $previous['error_log'] : array();
    $after = isset($current['error_log']) && is_array($current['error_log'])
        ? $current['error_log'] : array();
    $path = isset($after['path']) ? (string) $after['path'] : '';
    $beforePath = isset($before['path']) ? (string) $before['path'] : '';
    $beforeSize = isset($before['size']) ? $before['size'] : null;
    $afterSize = isset($after['size']) ? $after['size'] : null;

    if ($path === '' || $path !== $beforePath || !is_file($path) || !is_readable($path)
            || $beforeSize === null || $afterSize === null) {
        return $result;
    }

    $result['available'] = true;
    if ($afterSize < $beforeSize) {
        $result['rotated'] = true;
        return $result;
    }
    if ($afterSize <= $beforeSize) {
        return $result;
    }

    $deltaBytes = $afterSize - $beforeSize;
    $maxBytes = 524288;
    $start = $beforeSize;
    if ($deltaBytes > $maxBytes) {
        $start = $afterSize - $maxBytes;
        $result['truncated'] = true;
    }

    $handle = @fopen($path, 'rb');
    if ($handle === false || @fseek($handle, $start, SEEK_SET) !== 0) {
        if (is_resource($handle)) {
            fclose($handle);
        }
        $result['available'] = false;
        return $result;
    }

    $data = @fread($handle, $afterSize - $start);
    fclose($handle);
    if ($data === false || $data === '') {
        return $result;
    }

    $result['bytes'] = $deltaBytes;
    $counts = array();
    foreach (preg_split('/\r\n|\r|\n/', $data) as $line) {
        if (!preg_match('/(warning|notice|fatal|deprecated|uncaught|exception|error|e_warning|e_notice)/i', $line)) {
            continue;
        }

        $signature = MONITOR_CHANGES_normalizeLogLine($line);
        if ($signature === '') {
            continue;
        }

        if (!isset($counts[$signature])) {
            $counts[$signature] = 0;
        }
        $counts[$signature]++;
    }

    arsort($counts);
    $counts = array_slice($counts, 0, 10, true);
    foreach ($counts as $signature => $count) {
        $result['signatures'][] = array(
            'signature' => $signature,
            'count' => (int) $count
        );
    }

    return $result;
}

function MONITOR_CHANGES_report()
{
    $history = MONITOR_CHANGES_readHistory();
    $count = count($history);

    if ($count < 2) {
        return array(
            'ready' => false,
            'history_count' => $count,
            'previous' => ($count === 1) ? $history[0] : null,
            'current' => ($count === 1) ? $history[0] : null,
            'changes' => array(),
            'error_delta' => array()
        );
    }

    $previous = $history[$count - 2];
    $current = $history[$count - 1];

    return array(
        'ready' => true,
        'history_count' => $count,
        'previous' => $previous,
        'current' => $current,
        'changes' => MONITOR_CHANGES_compare($previous, $current),
        'error_delta' => MONITOR_CHANGES_errorDelta($previous, $current)
    );
}
