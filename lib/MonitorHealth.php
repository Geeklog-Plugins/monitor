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

/**
 * Build one normalized health result.
 *
 * @param string $id
 * @param string $label
 * @param string $status ok|info|warning|error
 * @param string $value
 * @param string $recommendation
 * @return array
 */
function MONITOR_HEALTH_result($id, $label, $status, $value, $recommendation)
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
        'recommendation' => (string) $recommendation
    );
}

/**
 * Collect initial Monitor 1.4.0 health checks.
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
    $status = 'ok';
    if ($percent < 5) {
        $status = 'error';
    } elseif ($percent < 10) {
        $status = 'warning';
    }

    return MONITOR_HEALTH_result(
        'disk.free',
        'Disk free space',
        $status,
        sprintf('%.1f%% (%s)', $percent, MONITOR_HEALTH_formatBytes($free)),
        $status === 'ok'
            ? 'Free space is above the current warning threshold.'
            : 'Free disk space is low; inspect logs, caches and backups.'
    );
}

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
