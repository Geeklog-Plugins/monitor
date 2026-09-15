<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | services.inc.php                                                          |
// |                                                                           |
// | Native Geeklog service callbacks. All services are read-only.             |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'services.inc.php') !== false) {
    die('This file can not be used on its own.');
}

require_once dirname(__FILE__) . '/lib/MonitorServices.php';
require_once dirname(__FILE__) . '/lib/MonitorContentActivity.php';
require_once dirname(__FILE__) . '/lib/MonitorPluginVersions.php';

function MONITOR_SERVICE_authorized()
{
    return SEC_hasRights('monitor.admin');
}

function MONITOR_SERVICE_rootAuthorized()
{
    return SEC_inGroup('Root');
}

function MONITOR_SERVICE_denied(&$output, &$svc_msg)
{
    $output = array();
    $svc_msg = array('Monitor service access denied.');

    return defined('PLG_RET_PERMISSION_DENIED') ? PLG_RET_PERMISSION_DENIED : -2;
}

function MONITOR_SERVICE_ok($value, &$output, &$svc_msg)
{
    $output = $value;
    $svc_msg = array();

    return defined('PLG_RET_OK') ? PLG_RET_OK : 0;
}

/**
 * Observe Geeklog content lifecycle notifications without copying content.
 * Signature defaults keep compatibility with older Geeklog callers while
 * accepting the sub_type argument added in newer Geeklog releases.
 */
function plugin_itemsaved_monitor($id, $type, $old_id = '', $sub_type = '')
{
    MONITOR_ACTIVITY_record('saved', $id, $type, $sub_type, $old_id);

    return true;
}

function plugin_itemdeleted_monitor($id, $type, $sub_type = '')
{
    MONITOR_ACTIVITY_record('deleted', $id, $type, $sub_type, '');

    return true;
}

/**
 * monitor.get_status
 */
function service_get_status_monitor($args, &$output, &$svc_msg)
{
    if (!MONITOR_SERVICE_authorized()) {
        return MONITOR_SERVICE_denied($output, $svc_msg);
    }

    return MONITOR_SERVICE_ok(MONITOR_SERVICE_status(), $output, $svc_msg);
}

/**
 * monitor.get_changes
 */
function service_get_changes_monitor($args, &$output, &$svc_msg)
{
    if (!MONITOR_SERVICE_authorized()) {
        return MONITOR_SERVICE_denied($output, $svc_msg);
    }

    return MONITOR_SERVICE_ok(MONITOR_SERVICE_changes(), $output, $svc_msg);
}

/**
 * monitor.get_plugins
 *
 * Optional argument:
 * - include_remote: bool, defaults to true. Remote checks use Monitor's cache
 *   and never force-refresh GitHub metadata.
 */
function service_get_plugins_monitor($args, &$output, &$svc_msg)
{
    if (!MONITOR_SERVICE_authorized()) {
        return MONITOR_SERVICE_denied($output, $svc_msg);
    }

    $args = is_array($args) ? $args : array();
    $plugins = MONITOR_SERVICE_plugins($args);
    $plugins = MONITOR_PLUGIN_VERSIONS_enrichServiceEnvelope($plugins);

    return MONITOR_SERVICE_ok($plugins, $output, $svc_msg);
}

/**
 * monitor.get_log_summary
 *
 * Optional argument:
 * - date: YYYY-MM-DD; defaults to the newest archived date.
 */
function service_get_log_summary_monitor($args, &$output, &$svc_msg)
{
    if (!MONITOR_SERVICE_authorized()) {
        return MONITOR_SERVICE_denied($output, $svc_msg);
    }

    $args = is_array($args) ? $args : array();

    return MONITOR_SERVICE_ok(MONITOR_SERVICE_logSummary($args), $output, $svc_msg);
}

/**
 * monitor.get_log_archives
 *
 * Optional arguments:
 * - date: YYYY-MM-DD
 * - limit: 1..365, defaults to 90 records.
 */
function service_get_log_archives_monitor($args, &$output, &$svc_msg)
{
    if (!MONITOR_SERVICE_authorized()) {
        return MONITOR_SERVICE_denied($output, $svc_msg);
    }

    $args = is_array($args) ? $args : array();

    return MONITOR_SERVICE_ok(MONITOR_SERVICE_logArchives($args), $output, $svc_msg);
}

/**
 * monitor.get_configuration_audit
 *
 * Mirrors the existing Configuration Audit privilege boundary: Root only.
 * Values, paths and alignment SQL are never exposed by this service.
 */
function service_get_configuration_audit_monitor($args, &$output, &$svc_msg)
{
    if (!MONITOR_SERVICE_rootAuthorized()) {
        return MONITOR_SERVICE_denied($output, $svc_msg);
    }

    return MONITOR_SERVICE_ok(MONITOR_SERVICE_configurationAudit(), $output, $svc_msg);
}
