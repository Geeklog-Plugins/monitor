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

/**
 * Archive one active log and truncate it only after the copy succeeds.
 *
 * @param string $path
 * @param string $archiveDate
 * @return array|false
 */
function MONITOR_LOG_archiveOne($path, $archiveDate)
{
    if (!is_file($path) || !is_readable($path) || !is_writable($path)) {
        return false;
    }

    $size = @filesize($path);
    if ($size === false) {
        return false;
    }

    if ($size <= 0) {
        return array(
            'file' => '',
            'log' => basename($path),
            'size' => 0,
            'path' => ''
        );
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

    return array(
        'file' => basename($archive),
        'log' => $filename,
        'size' => (int) $size,
        'path' => $archive
    );
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

function MONITOR_LOG_formatBytes($bytes)
{
    $bytes = (float) $bytes;
    $units = array('B', 'KiB', 'MiB', 'GiB');
    $index = 0;

    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }

    return number_format($bytes, $index === 0 ? 0 : 1) . ' ' . $units[$index];
}

function MONITOR_LOG_signature($line)
{
    $line = trim((string) $line);
    $line = preg_replace('/^\[[^\]]+\]\s*/', '', $line);
    $line = preg_replace('/\b0x[0-9a-f]+\b/i', '0x*', $line);
    $line = preg_replace('/\b\d+\b/', '#', $line);
    $line = preg_replace('/\s+/', ' ', $line);

    if (strlen($line) > 180) {
        $line = substr($line, 0, 177) . '...';
    }

    return $line;
}

/**
 * Build bounded statistics for one archive.
 *
 * The scan stops after 50,000 lines so a pathological log cannot make the
 * scheduled task unbounded. Pattern statistics are intended as hints only.
 *
 * @param string $path
 * @return array
 */
function MONITOR_LOG_archiveStats($path)
{
    $stats = array(
        'lines' => 0,
        'issue_lines' => 0,
        'truncated' => false,
        'patterns' => array()
    );

    if (!is_file($path) || !is_readable($path)) {
        return $stats;
    }

    $handle = @fopen($path, 'rb');
    if ($handle === false) {
        return $stats;
    }

    $limit = 50000;
    while (($line = fgets($handle)) !== false) {
        $stats['lines']++;
        if (preg_match('/\b(error|warning|exception|fatal|critical)\b/i', $line)) {
            $stats['issue_lines']++;
            $signature = MONITOR_LOG_signature($line);
            if ($signature !== '') {
                if (!isset($stats['patterns'][$signature])) {
                    $stats['patterns'][$signature] = 0;
                }
                $stats['patterns'][$signature]++;
            }
        }

        if ($stats['lines'] >= $limit) {
            $stats['truncated'] = !feof($handle);
            break;
        }
    }
    fclose($handle);

    arsort($stats['patterns']);
    $stats['patterns'] = array_slice($stats['patterns'], 0, 5, true);

    return $stats;
}

/**
 * Send one concise daily summary for the archives created by a rotation.
 *
 * The email setting is historical Monitor behavior. The summary is now based
 * on the immutable daily archives instead of emailing and then clearing the
 * only copy of the active log.
 *
 * @param array $rotation
 * @return bool
 */
function MONITOR_LOG_sendDailySummary($rotation)
{
    global $_CONF, $_MONITOR_CONF, $LANG_MONITOR_1;

    if (empty($_MONITOR_CONF['emails'])
            || empty($rotation['archive_date'])
            || empty($rotation['archives'])
            || !is_array($rotation['archives'])) {
        return false;
    }

    $archiveUrl = $_CONF['site_admin_url'] . '/plugins/monitor/log-archives.php';
    $date = (string) $rotation['archive_date'];
    $rows = '';
    $topPatterns = array();

    foreach ($rotation['archives'] as $archive) {
        if (empty($archive['path']) || empty($archive['file'])) {
            continue;
        }

        $stats = MONITOR_LOG_archiveStats($archive['path']);
        $viewUrl = $archiveUrl . '?file=' . rawurlencode($archive['file']);
        $rows .= '<tr>'
              . '<td><code>' . htmlspecialchars($archive['log'], ENT_QUOTES, 'UTF-8') . '</code></td>'
              . '<td>' . htmlspecialchars(MONITOR_LOG_formatBytes($archive['size']), ENT_QUOTES, 'UTF-8') . '</td>'
              . '<td>' . (int) $stats['lines'] . (!empty($stats['truncated']) ? '+' : '') . '</td>'
              . '<td>' . (int) $stats['issue_lines'] . '</td>'
              . '<td><a href="' . htmlspecialchars($viewUrl, ENT_QUOTES, 'UTF-8') . '">'
              . htmlspecialchars($LANG_MONITOR_1['log_archive_view'], ENT_QUOTES, 'UTF-8') . '</a></td>'
              . '</tr>';

        if (strcasecmp($archive['log'], 'error.log') === 0) {
            foreach ($stats['patterns'] as $pattern => $count) {
                if (!isset($topPatterns[$pattern])) {
                    $topPatterns[$pattern] = 0;
                }
                $topPatterns[$pattern] += (int) $count;
            }
        }
    }

    if ($rows === '') {
        return false;
    }

    arsort($topPatterns);
    $topPatterns = array_slice($topPatterns, 0, 5, true);

    $message = '<h2>' . htmlspecialchars($LANG_MONITOR_1['log_archive_title'], ENT_QUOTES, 'UTF-8')
             . ' — ' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '</h2>'
             . '<table border="1" cellpadding="6" cellspacing="0">'
             . '<thead><tr>'
             . '<th>' . htmlspecialchars($LANG_MONITOR_1['log_archive_log'], ENT_QUOTES, 'UTF-8') . '</th>'
             . '<th>' . htmlspecialchars($LANG_MONITOR_1['log_archive_size'], ENT_QUOTES, 'UTF-8') . '</th>'
             . '<th>Lines</th><th>' . htmlspecialchars($LANG_MONITOR_1['changes_logs'], ENT_QUOTES, 'UTF-8') . '</th>'
             . '<th>' . htmlspecialchars($LANG_MONITOR_1['log_archive_actions'], ENT_QUOTES, 'UTF-8') . '</th>'
             . '</tr></thead><tbody>' . $rows . '</tbody></table>';

    if (!empty($topPatterns)) {
        $message .= '<h3>' . htmlspecialchars($LANG_MONITOR_1['changes_logs'], ENT_QUOTES, 'UTF-8') . '</h3><ol>';
        foreach ($topPatterns as $pattern => $count) {
            $message .= '<li><strong>' . (int) $count . ' ×</strong> '
                     . htmlspecialchars($pattern, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        $message .= '</ol>';
    }

    $message .= '<p><a href="' . htmlspecialchars($archiveUrl, ENT_QUOTES, 'UTF-8') . '">'
             . htmlspecialchars($LANG_MONITOR_1['log_archive_title'], ENT_QUOTES, 'UTF-8')
             . '</a></p>';

    $sent = false;
    foreach (explode(',', $_MONITOR_CONF['emails']) as $address) {
        $contact = trim($address);
        if ($contact === '') {
            continue;
        }

        $mailResult = COM_mail(
            $contact,
            $_CONF['site_name'] . ' | ' . $LANG_MONITOR_1['log_archive_title'] . ' | ' . $date,
            $message,
            '',
            true
        );
        if ($mailResult !== false) {
            $sent = true;
        }
    }

    return $sent;
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
        'archive_date' => '',
        'archives' => array(),
        'email_sent' => false,
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

    $result['archive_date'] = $lastDate;
    $logs = glob(rtrim($_CONF['path_log'], '/\\') . DIRECTORY_SEPARATOR . '*.log');
    if (is_array($logs)) {
        foreach ($logs as $path) {
            $archive = MONITOR_LOG_archiveOne($path, $lastDate);
            if ($archive === false) {
                $result['failed']++;
                continue;
            }

            if (!empty($archive['file'])) {
                $result['archives'][] = $archive;
                $result['rotated']++;
            }
        }
    }

    if ($result['failed'] === 0) {
        MONITOR_LOG_writeState(array(
            'last_rotation_date' => $today,
            'updated_at' => time()
        ));
        if (!empty($result['archives'])) {
            $result['email_sent'] = MONITOR_LOG_sendDailySummary($result);
        }
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
