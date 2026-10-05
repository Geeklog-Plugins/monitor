<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.5.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorConfigCompat.php                                                   |
// +---------------------------------------------------------------------------+

/**
 * Repair Monitor configuration metadata idempotently.
 *
 * This repair is deliberately independent of the persisted plugin version.
 * Development or early 1.5.0 packages may already have pi_version = 1.5.0,
 * so relying on plugin_upgrade_monitor() would leave those sites unchanged.
 *
 * Existing administrator values are preserved. Missing settings are created
 * with safe defaults, while Geeklog 2.2.x configuration metadata is normalized
 * for the native Configuration UI.
 *
 * @return bool
 */
function MONITOR_repairConfiguration()
{
    global $_CONF, $_TABLES;

    if (!isset($_TABLES['conf_values'])) {
        return false;
    }

    $group = 'monitor';
    $table = $_TABLES['conf_values'];
    $c = config::get_instance();

    $defaults = array(
        'emails' => isset($_CONF['site_mail']) ? (string) $_CONF['site_mail'] : '',
        'repository' => 'Geeklog-Plugins',
        'github_token' => ''
    );

    $sort = array(
        'emails' => 10,
        'repository' => 20,
        'github_token' => 30
    );

    foreach ($defaults as $name => $defaultValue) {
        $safeName = addslashes($name);
        $result = DB_query(
            "SELECT name FROM {$table} "
            . "WHERE group_name = 'monitor' AND name = '{$safeName}' LIMIT 1",
            1
        );
        $row = $result ? DB_fetchArray($result) : false;

        if (!is_array($row) || empty($row['name'])) {
            $c->add(
                $name,
                $defaultValue,
                'text',
                0,
                0,
                null,
                $sort[$name],
                true,
                $group,
                0
            );
        }
    }

    if (!defined('VERSION') || COM_versionCompare(VERSION, '2.2.0', '<')) {
        return true;
    }

    $result = DB_query(
        "SELECT name FROM {$table} "
        . "WHERE group_name = 'monitor' AND type = 'tab' AND name = 'tab_main' LIMIT 1",
        1
    );

    if (!$result) {
        return false;
    }

    $tab = DB_fetchArray($result);
    if (!is_array($tab) || empty($tab['name'])) {
        $c->add('tab_main', null, 'tab', 0, 0, null, 0, true, $group, 0);

        $verify = DB_query(
            "SELECT name FROM {$table} "
            . "WHERE group_name = 'monitor' AND type = 'tab' AND name = 'tab_main' LIMIT 1",
            1
        );
        $verifiedTab = $verify ? DB_fetchArray($verify) : false;
        if (!is_array($verifiedTab) || empty($verifiedTab['name'])) {
            return false;
        }
    }

    $updated = DB_query(
        "UPDATE {$table} SET selectionArray = -1, tab = 0 "
        . "WHERE group_name = 'monitor' "
        . "AND name IN ('emails', 'repository', 'github_token') "
        . "AND (selectionArray <> -1 OR tab <> 0)",
        1
    );

    return (bool) $updated;
}

/**
 * Backward-compatible wrapper retained for older Monitor callers/tests.
 *
 * @return bool
 */
function MONITOR_repairConfiguration140()
{
    return MONITOR_repairConfiguration();
}

