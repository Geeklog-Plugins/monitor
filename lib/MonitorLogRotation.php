<?php

/**
 * Daily Geeklog log rotation for Monitor.
 *
 * Active .log files remain in Geeklog's configured log directory. Completed
 * daily logs are copied into a site-specific archive below path_data and the
 * active file is truncated only after the archive copy succeeds.
 *
 * Compatible with PHP 5.6.
 */

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorlogrotation.php') !== false) {
    die('This file can not be used on its own.');
}

function MONITOR_LOG_siteKey()
{
    global $_CONF;

    $host = isset($_CONF['site_url']) ? (string) $_CONF['site_url'] : '';
    $logPath = isset($_CONF['path_log']) ? (string) $_CONF['path_log'] : '';

    return substr(sha1($host . '|' . $logPath), 0, 16);
}

function MONITOR_LOG_archiveDir()
{
    global $_CONF;

    if (empty($_CONF['path_data'])) {
        return '';
    }

    return rtrim($_CONF['path_data'], '/\\')
        . DIRECTORY_SEPARATOR . 'monitor-log-archives-'
        . MONITOR_LOG_siteKey() . DIRECTORY_SEPARATOR;
}

function MONITOR_LOG_retentionDays()
{
    global $_MONITOR_CONF;

    $days = isset($_MONITOR_CONF['log_retention_days'])
        ? (int) $_MONITOR_CONF['log_retention_days']
        : 90;

    if ($days < 7) {
        $days = 7;
    } elseif ($days > 3650) {
        $days = 3650;
    }

    return $days;
}

function MONITOR_LOG_ensureArchiveDir()
{
    $dir = MONITOR_LOG_archiveDir();
    if ($dir === '') {
        return false;
    }

    if (is_dir($dir)) {
        return is_writable($dir);
    }

    return @mkdir($dir, 0750, true) && is_writable($dir);
}

function MONITOR_LOG_statePath()
{
    return MONITOR_LOG_archiveDir() . '.rotation.json';
}

function MONITOR_LOG_readState()
{
    $path = MONITOR_LOG_statePath();
    if (!is_file($path) || !is_readable($path)) {
        return array();
    }

    $json = @file_get_contents($path);
    $data = $json !== false ? json_decode($json, true) : null;

    return is_array($data) ? $data : array();
}

function MONITOR_LOG_writeState($state)
{
    if (!MONITOR_LOG_ensureArchiveDir()) {
        return false;
    }

    $json = json_encode($state);
    if ($json === false) {
        return false;
    }

    $path = MONITOR_LOG_statePath();
    $tmp = $path . '.tmp';
    if (@file_put_contents($tmp, $json, LOCK_EX) === false) {
        return false;
    }

    if (!@rename($tmp, $path)) {
        @unlink($tmp);
        return false;
    }

    return true;
}

function MONITOR_LOG_safeName($name)
{
    $name = basename((string) $name);
    $name = preg_replace('/[^A-Za-z0-9_.-]+/', '-', $name);

    return trim($name, '-');
}

function MONITOR_LOG_archiveOne($path, $archiveDate)
{
    if (!is_file($path) || !is_readable($path) || !is_writable($path)) {
        return false;
    }

    $size = @filesize($path);
    if ($size === false || $size <= 0) {
        return true;
    }

    if (!MONITOR_LOG_ensureArchiveDir()) {
        return false;
    }

    $filename = MONITOR_LOG_safeName(basename($path));
    $base = preg_replace('/\.log$/i', '', $filename);
    $archive = MONITOR_LOG_archiveDir() . $base . '-' . $archiveDate . '.log';
    $suffix = 2;
    while (file_exists($archive)) {
        $archive = MONITOR_LOG_archiveDir() . $base . '-' . $archiveDate
                 . '-' . $suffix . '.log';
        $suffix++;
    }

    if (!@copy($path, $archive)) {
        return false;
    }

    @chmod($archive, 0640);

    $handle = @fopen($path, 'c+');
    if ($handle === false) {
        @unlink($archive);
        return false;
    }

    $locked = @flock($handle, LOCK_EX);
    $truncated = $locked ? @ftruncate($handle, 0) : false;
    if ($locked) {
        @fflush($handle);
        @flock($handle, LOCK_UN);
    }
    @fclose($handle);

    if (!$truncated) {
        @unlink($archive);
        return false;
    }

    return true;
}

function MONITOR_LOG_cleanupArchives($retentionDays)
{
    $dir = MONITOR_LOG_archiveDir();
    if (!is_dir($dir)) {
        return 0;
    }

    $cutoff = time() - ((int) $retentionDays * 86400);
    $removed = 0;
    $files = glob($dir . '*.log');
    if (!is_array($files)) {
        return 0;
    }

    foreach ($files as $file) {
        $mtime = @filemtime($file);
        if ($mtime !== false && $mtime < $cutoff && @unlink($file)) {
            $removed++;
        }
    }

    return $removed;
}

/**
 * Rotate active Geeklog logs at most once for each calendar date.
 *
 * The first invocation creates a baseline and does not truncate current logs.
 * A later invocation on a different date archives the accumulated active logs
 * under the preceding baseline date, then starts new empty active files.
 *
 * @return array
 */
function MONITOR_LOG_rotateDaily()
{
    global $_CONF;

    $today = date('Y-m-d');
    $result = array(
        'rotated' => 0,
        'failed' => 0,
        'removed' => 0,
        'date' => $today,
        'baseline' => false
    );

    if (empty($_CONF['path_log']) || !is_dir($_CONF['path_log'])) {
        return $result;
    }

    if (!MONITOR_LOG_ensureArchiveDir()) {
        return $result;
    }

    $state = MONITOR_LOG_readState();
    $lastDate = isset($state['last_rotation_date'])
        ? (string) $state['last_rotation_date']
        : '';

    if ($lastDate === '') {
        MONITOR_LOG_writeState(array(
            'last_rotation_date' => $today,
            'updated_at' => time()
        ));
        $result['baseline'] = true;
        $result['removed'] = MONITOR_LOG_cleanupArchives(MONITOR_LOG_retentionDays());
        return $result;
    }

    if ($lastDate === $today) {
        $result['removed'] = MONITOR_LOG_cleanupArchives(MONITOR_LOG_retentionDays());
        return $result;
    }

    $logs = glob(rtrim($_CONF['path_log'], '/\\') . DIRECTORY_SEPARATOR . '*.log');
    if (is_array($logs)) {
        foreach ($logs as $path) {
            if (MONITOR_LOG_archiveOne($path, $lastDate)) {
                $size = @filesize($path);
                if ($size === 0) {
                    $result['rotated']++;
                }
            } else {
                $result['failed']++;
            }
        }
    }

    if ($result['failed'] === 0) {
        MONITOR_LOG_writeState(array(
            'last_rotation_date' => $today,
            'updated_at' => time()
        ));
    }

    $result['removed'] = MONITOR_LOG_cleanupArchives(MONITOR_LOG_retentionDays());

    return $result;
}

function MONITOR_LOG_listArchives()
{
    $dir = MONITOR_LOG_archiveDir();
    $archives = array();
    if (!is_dir($dir)) {
        return $archives;
    }

    $files = glob($dir . '*.log');
    if (!is_array($files)) {
        return $archives;
    }

    foreach ($files as $path) {
        $name = basename($path);
        if (!preg_match('/^(.+)-(\d{4}-\d{2}-\d{2})(?:-(\d+))?\.log$/', $name, $m)) {
            continue;
        }
        $archives[] = array(
            'file' => $name,
            'log' => $m[1] . '.log',
            'date' => $m[2],
            'size' => @filesize($path),
            'mtime' => @filemtime($path)
        );
    }

    usort($archives, function ($a, $b) {
        return strcmp($b['file'], $a['file']);
    });

    return $archives;
}

function MONITOR_LOG_archivePath($filename)
{
    $filename = basename((string) $filename);
    if ($filename === '' || !preg_match('/^[A-Za-z0-9_.-]+\.log$/', $filename)) {
        return '';
    }

    $path = MONITOR_LOG_archiveDir() . $filename;

    return is_file($path) && is_readable($path) ? $path : '';
}
