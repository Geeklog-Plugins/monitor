<?php

/**
 * Monitor <-> Ban integration adapter.
 *
 * Monitor must not depend on Ban's private SQL schema. This adapter is the only
 * place where Monitor may call a public/stable Ban capability when available.
 *
 * Compatible with PHP 5.6.
 */

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorbanadapter.php') !== false) {
    die('This file can not be used on its own.');
}

/**
 * Return Ban integration capabilities visible to Monitor.
 *
 * @return array
 */
function MONITOR_BAN_capabilities()
{
    global $_PLUGINS;

    $installed = isset($_PLUGINS)
        && is_array($_PLUGINS)
        && in_array('ban', $_PLUGINS, true);

    return array(
        'installed' => $installed,
        'enabled' => $installed,
        'request_ip_ban' => $installed && function_exists('BAN_insertREMOT_ADDR'),
        'query_ip_status' => false,
        'temporary_ban' => $installed && function_exists('BAN_insertREMOT_ADDR')
    );
}

/**
 * Return true when Monitor may request an IP ban through Ban.
 *
 * @return bool
 */
function MONITOR_BAN_canRequestIpBan()
{
    $capabilities = MONITOR_BAN_capabilities();

    return !empty($capabilities['request_ip_ban']);
}

/**
 * Ask Ban to create an IP rule.
 *
 * This method intentionally performs no direct Ban table access. It accepts
 * only a validated IP address and delegates rule creation to Ban's own API.
 *
 * @param string $ip
 * @param int    $status Ban status constant/value
 * @param string $reason
 * @return bool
 */
function MONITOR_BAN_requestIpBan($ip, $status, $reason)
{
    if (!MONITOR_BAN_canRequestIpBan()) {
        return false;
    }

    $ip = trim((string) $ip);
    if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
        return false;
    }

    $status = (int) $status;
    $reason = trim((string) $reason);

    /*
     * Ban 2.0.x exposes BAN_insertREMOT_ADDR(). Keep that implementation detail
     * isolated here so Monitor can switch to a future formal Ban capability/API
     * without touching dashboard or detection code.
     */
    return (bool) BAN_insertREMOT_ADDR($ip, $status, $reason);
}

/**
 * Return the currently available Ban plugin code version when Geeklog can
 * provide it. An empty string means unknown/unavailable.
 *
 * @return string
 */
function MONITOR_BAN_version()
{
    if (!function_exists('PLG_chkVersion')) {
        return '';
    }

    $version = PLG_chkVersion('ban');

    return is_string($version) ? $version : '';
}

?>
