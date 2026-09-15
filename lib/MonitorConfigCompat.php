<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorConfigCompat.php                                                   |
// +---------------------------------------------------------------------------+

/**
 * Repair configuration rows created by early Monitor 1.4.0 builds.
 *
 * The optional github_token setting is added idempotently on every supported
 * Geeklog generation. The tab/selectionArray repair remains limited to 2.2.x,
 * where that hierarchy is required by the native configuration UI.
 *
 * @return bool
 */
function MONITOR_repairConfiguration140()
{
    global $_TABLES;

    if (!isset($_TABLES['conf_values'])) {
        return false;
    }

    $group = 'monitor';
    $table = $_TABLES['conf_values'];

    $tokenResult = DB_query(
        "SELECT name FROM {$table} "
        . "WHERE group_name = 'monitor' AND name = 'github_token' LIMIT 1",
        1
    );
    $tokenRow = $tokenResult ? DB_fetchArray($tokenResult) : false;
    if (!is_array($tokenRow) || empty($tokenRow['name'])) {
        $c = config::get_instance();
        $c->add('github_token', '', 'text', 0, 0, null, 30, true, $group, 0);
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
        $c = config::get_instance();
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

    if (!$updated) {
        return false;
    }

    return true;
}
