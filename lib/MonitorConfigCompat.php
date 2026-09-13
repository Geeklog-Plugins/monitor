<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorConfigCompat.php                                                   |
// +---------------------------------------------------------------------------+

/**
 * Repair configuration rows created by early Monitor 1.4.0 builds.
 *
 * Official Geeklog plugins such as Polls use the hierarchy
 * subgroup -> tab -> fieldset -> setting and pass NULL when a setting has no
 * selection array. Early Monitor 1.4.0 builds omitted tab_main and stored 0 as
 * selectionArray for text fields, which breaks Geeklog 2.2.x configuration UI.
 *
 * This repair is idempotent and intentionally limited to Geeklog 2.2.x.
 *
 * @return bool
 */
function MONITOR_repairConfiguration140()
{
    global $_TABLES;

    if (!defined('VERSION') || COM_versionCompare(VERSION, '2.2.0', '<')) {
        return true;
    }

    if (!isset($_TABLES['conf_values'])) {
        return false;
    }

    $group = 'monitor';
    $table = $_TABLES['conf_values'];

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
        . "AND name IN ('emails', 'repository') "
        . "AND (selectionArray <> -1 OR tab <> 0)",
        1
    );

    if (!$updated) {
        return false;
    }

    return true;
}
