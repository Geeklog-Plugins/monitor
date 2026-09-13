<?php

/**
 * Structured health checks for Monitor.
 *
 * Checks are intentionally read-only and independent. One failed optional
 * check must not break the administration dashboard.
 *
 * Compatible with PHP 5.6.
 */

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorhealth.php') !== false) {
    die('This file can not be used on its own.');
}

require_once dirname(__FILE__) . '/MonitorCompat.php';
require_once dirname(__FILE__) . '/MonitorMediaDiagnostics.php';

/**
 * Build one normalized health result.
 *
 * @param string $id
 * @param string $label
 * @param string $status ok|info|warning|error
 * @param string $value
 * @param string $recommendation
 * @param array  $details optional structured UI/service details
 * @return array
 */
function MONITOR_HEALTH_result($id, $label, $status, $value, $recommendation, $details = array())
{
    $allowed = array('ok', 'info', 'warning', 'error');
    if (!in_array($status, $allowed, true)) {
        $status = 'info';
    }

    return array(
        'id' => (string) $id,
        'label' => (string) $label,
        'status' => $status,
        'value' => (string) $value,
        'recommendation' => (string) $recommendation,
        'details' => is_array($details) ? $details : array()
    );
}

/**
 * Collect Monitor 1.4.0 health checks.
 *
 * @return array
 */
function MONITOR_HEALTH_collect()
{
    global $_CONF, $_TABLES;

    $checks = array();

    $phpSupported = version_compare(PHP_VERSION, '5.6.0', '>=')
        && version_compare(PHP_VERSION, '8.1.99', '<=');

    $checks[] = MONITOR_HEALTH_result(
        'runtime.php',
        'PHP',
        $phpSupported ? 'ok' : 'warning',
        PHP_VERSION,
        $phpSupported
            ? 'PHP is inside the tested Monitor 1.4.0 range.'
            : 'Monitor 1.4.0 is currently tested on PHP 5.6 through 8.1.'
    );

    $geeklogVersion = defined('VERSION') ? VERSION : 'unknown';
    $geeklogStatus = 'info';
    if ($geeklogVersion !== 'unknown') {
        $geeklogStatus = COM_versionCompare($geeklogVersion, '2.1.1', '>=')
            && COM_versionCompare($geeklogVersion, '2.2.2', '<=')
            ? 'ok' : 'warning';
    }

    $checks[] = MONITOR_HEALTH_result(
        'runtime.geeklog',
        'Geeklog',
        $geeklogStatus,
        $geeklogVersion,
        'Monitor 1.4.0 currently targets Geeklog 2.1.1 through 2.2.2.'
    );

    $checks[] = MONITOR_HEALTH_pathCheck(
        'path.data',
        'Data path',
        isset($_CONF['path_data']) ? $_CONF['path_data'] : ''
    );

    $checks[] = MONITOR_HEALTH_pathCheck(
        'path.log',
        'Log path',
        isset($_CONF['path_log']) ? $_CONF['path_log'] : ''
    );

    $checks[] = MONITOR_HEALTH_logSizeCheck(
        'log.error',
        'error.log',
        isset($_CONF['path_log']) ? $_CONF['path_log'] . 'error.log' : '',
        10 * 1024 * 1024
    );

    $checks[] = MONITOR_HEALTH_diskCheck();
    $checks[] = MONITOR_HEALTH_oversizedImagesCheck();

    if (isset($_TABLES['plugins'])) {
        $installedVersion = DB_getItem(
            $_TABLES['plugins'],
            'pi_version',
            "pi_name = 'monitor'"
        );
        $codeVersion = function_exists('plugin_chkVersion_monitor')
            ? plugin_chkVersion_monitor()
            : '';

        $state = ($installedVersion !== '' && $codeVersion !== '' && $installedVersion == $codeVersion)
            ? 'ok' : 'warning';

        $checks[] = MONITOR_HEALTH_result(
            'monitor.version_state',
            'Monitor state',
            $state,
            'DB ' . $installedVersion . ' / code ' . $codeVersion,
            $state === 'ok'
                ? 'Monitor code and persisted plugin versions match.'
                : 'Run the Monitor plugin upgrade for this site before using new persisted features.'
        );
    }

    if (function_exists('MONITOR_BAN_capabilities')) {
        $capabilities = MONITOR_BAN_capabilities();
        $banValue = !empty($capabilities['installed']) ? 'available' : 'not installed';
        $banRecommendation = !empty($capabilities['installed'])
            ? 'Ban can remain the enforcement engine while Monitor focuses on detection and diagnosis.'
            : 'Ban is optional. Install or modernize it only when centralized access blocking is required.';

        $checks[] = MONITOR_HEALTH_result(
            'security.ban',
            'Ban integration',
            !empty($capabilities['installed']) ? 'ok' : 'info',
            $banValue,
            $banRecommendation
        );
    }

    return $checks;
}

/**
 * Check that a configured path exists and is writable.
 *
 * @param string $id
 * @param string $label
 * @param string $path
 * @return array
 */
function MONITOR_HEALTH_pathCheck($id, $label, $path)
{
    if ($path === '' || !is_dir($path)) {
        return MONITOR_HEALTH_result(
            $id,
            $label,
            'error',
            $path === '' ? 'not configured' : $path,
            'Check the active site configuration and directory path.'
        );
    }

    if (!is_writable($path)) {
        return MONITOR_HEALTH_result(
            $id,
            $label,
            'warning',
            $path,
            'The directory exists but is not writable by PHP.'
        );
    }

    return MONITOR_HEALTH_result(
        $id,
        $label,
        'ok',
        $path,
        'Directory is available and writable.'
    );
}

/**
 * Check one log size without reading its contents.
 *
 * @param string $id
 * @param string $label
 * @param string $path
 * @param int    $warningBytes
 * @return array
 */
function MONITOR_HEALTH_logSizeCheck($id, $label, $path, $warningBytes)
{
    if ($path === '' || !is_file($path)) {
        return MONITOR_HEALTH_result(
            $id,
            $label,
            'info',
            'not present',
            'No log file was found at the configured location.'
        );
    }

    $size = filesize($path);
    if ($size === false) {
        return MONITOR_HEALTH_result(
            $id,
            $label,
            'warning',
            'unreadable size',
            'Check permissions on the log file.'
        );
    }

    $status = ($size >= (int) $warningBytes) ? 'warning' : 'ok';

    return MONITOR_HEALTH_result(
        $id,
        $label,
        $status,
        MONITOR_HEALTH_formatBytes($size),
        $status === 'ok'
            ? 'Log size is below the current warning threshold.'
            : 'Inspect the recent log tail and consider rotation after diagnosis.'
    );
}

/**
 * Check free space for the active site's data path filesystem.
 *
 * Percentage alone is misleading on large volumes: 5% free on a multi-TB
 * filesystem may still represent hundreds of gigabytes. Monitor therefore
 * combines relative and absolute thresholds before raising attention.
 *
 * @return array
 */
function MONITOR_HEALTH_diskCheck()
{
    global $_CONF;

    $path = isset($_CONF['path_data']) ? $_CONF['path_data'] : '';
    if ($path === '' || !is_dir($path) || !function_exists('disk_free_space')) {
        return MONITOR_HEALTH_result(
            'disk.free',
            'Disk free space',
            'info',
            'unknown',
            'Disk free space is not available in this environment.'
        );
    }

    $free = @disk_free_space($path);
    $total = function_exists('disk_total_space') ? @disk_total_space($path) : false;

    if ($free === false || $total === false || $total <= 0) {
        return MONITOR_HEALTH_result(
            'disk.free',
            'Disk free space',
            'info',
            'unknown',
            'Disk statistics could not be read.'
        );
    }

    $percent = ($free / $total) * 100;
    $oneGiB = 1024 * 1024 * 1024;
    $warningAbsolute = 10 * $oneGiB;
    $percentageRelevantBelow = 20 * $oneGiB;

    $status = 'ok';
    if ($free < $oneGiB) {
        $status = 'error';
    } elseif ($free < $warningAbsolute || ($percent < 10 && $free < $percentageRelevantBelow)) {
        $status = 'warning';
    }

    $recommendation = 'Free disk capacity is sufficient.';
    if ($status === 'error') {
        $recommendation = 'Free disk space is critically low; inspect logs, caches and backups immediately.';
    } elseif ($status === 'warning') {
        $recommendation = 'Free disk space is becoming low; inspect logs, caches and backups.';
    } elseif ($percent < 10) {
        $recommendation = 'The free-space percentage is low, but the absolute free capacity remains sufficient.';
    }

    return MONITOR_HEALTH_result(
        'disk.free',
        'Disk free space',
        $status,
        sprintf('%.1f%% (%s)', $percent, MONITOR_HEALTH_formatBytes($free)),
        $recommendation
    );
}

/**
 * Read-only diagnostic for oversized images.
 *
 * The scan is deliberately bounded so opening the Monitor dashboard cannot
 * turn into an unbounded recursive filesystem operation on large sites.
 * No file is ever modified by this check.
 *
 * Current warning thresholds:
 * - width or height greater than 1600 pixels
 * - file size greater than 2 MiB
 *
 * @return array
 */
function MONITOR_HEALTH_oversizedImagesCheck()
{
    global $_CONF;

    $root = isset($_CONF['path_images']) ? $_CONF['path_images'] : '';
    $maxFiles = 2000;
    $maxDimension = 1600;
    $maxBytes = 2 * 1024 * 1024;

    if ($root === '' || !is_dir($root) || !is_readable($root)) {
        return MONITOR_HEALTH_result(
            'images.oversized',
            'Oversized images',
            'info',
            'not scanned',
            'The configured image directory is unavailable or unreadable.'
        );
    }

    $media = MONITOR_MEDIA_oversizedImages($maxFiles, 20);
    if (empty($media['available'])) {
        return MONITOR_HEALTH_result(
            'images.oversized',
            'Oversized images',
            'warning',
            'scan interrupted',
            'Monitor could not complete the read-only image diagnostic. Check image directory permissions.'
        );
    }

    $checked = isset($media['checked']) ? (int) $media['checked'] : 0;
    $oversized = isset($media['total']) ? (int) $media['total'] : 0;
    $partial = !empty($media['partial']);

    if ($checked === 0) {
        return MONITOR_HEALTH_result(
            'images.oversized',
            'Oversized images',
            'info',
            'no supported images found',
            'No JPEG, PNG, GIF or WebP image was found in the configured image directory.'
        );
    }

    $value = $oversized . ' / ' . $checked;
    if ($partial) {
        $value .= ' (partial)';
    }

    if ($oversized > 0) {
        $fileManager = isset($_CONF['site_url'])
            ? rtrim($_CONF['site_url'], '/') . '/filemanager/index.php?Type=Root'
            : '/filemanager/index.php?Type=Root';

        return MONITOR_HEALTH_result(
            'images.oversized',
            'Oversized images',
            'warning',
            $value,
            $oversized . ' image(s) exceed the recommended dimensions or file size.',
            array(
                'type' => 'oversized_images',
                'items' => $media['items'],
                'items_truncated' => !empty($media['items_truncated']),
                'total' => $oversized,
                'checked' => $checked,
                'partial_scan' => $partial,
                'threshold_dimension' => $maxDimension,
                'threshold_bytes' => $maxBytes,
                'file_manager_url' => $fileManager
            )
        );
    }

    return MONITOR_HEALTH_result(
        'images.oversized',
        'Oversized images',
        $partial ? 'info' : 'ok',
        $value,
        $partial
            ? 'No oversized image was found in the bounded sample. The scan stopped at its safety limit.'
            : 'No oversized image was detected above the current 1600 px / 2 MiB thresholds.'
    );
}

/**
 * Summarize check states.
 *
 * @param array $checks
 * @return array
 */
function MONITOR_HEALTH_summary($checks)
{
    $summary = array(
        'ok' => 0,
        'info' => 0,
        'warning' => 0,
        'error' => 0
    );

    foreach ($checks as $check) {
        $status = isset($check['status']) ? $check['status'] : 'info';
        if (!isset($summary[$status])) {
            $status = 'info';
        }
        $summary[$status]++;
    }

    return $summary;
}

/**
 * Human-readable byte count.
 *
 * @param int|float $bytes
 * @return string
 */
function MONITOR_HEALTH_formatBytes($bytes)
{
    $bytes = (float) $bytes;
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    $index = 0;

    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }

    return sprintf($index === 0 ? '%.0f %s' : '%.1f %s', $bytes, $units[$index]);
}
