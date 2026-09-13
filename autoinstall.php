<?php

/**
 * Monitor plugin autoinstall helpers.
 *
 * Compatibility target for Monitor 1.4.0:
 * - Geeklog 2.1.1 through 2.2.2
 * - PHP 5.6 through 8.1
 *
 * @package Monitor
 */

function plugin_autoinstall_monitor($pi_name)
{
    $pi_name = 'monitor';
    $pi_display_name = 'Monitor';
    $pi_admin = $pi_display_name . ' Admin';

    $info = array(
        'pi_name'         => $pi_name,
        'pi_display_name' => $pi_display_name,
        'pi_version'      => '1.4.0',
        'pi_gl_version'   => '2.1.1',
        'pi_homepage'     => 'https://github.com/hostellerie/monitor'
    );

    $groups = array(
        $pi_admin => 'Users in this group can administer the '
                     . $pi_display_name . ' plugin'
    );

    $features = array(
        $pi_name . '.admin' => 'Full access to ' . $pi_display_name . ' plugin'
    );

    $mappings = array(
        $pi_name . '.admin' => array($pi_admin)
    );

    $tables = array(
        'monitor_ban'
    );

    return array(
        'info'     => $info,
        'groups'   => $groups,
        'features' => $features,
        'mappings' => $mappings,
        'tables'   => $tables
    );
}

function plugin_load_configuration_monitor($pi_name)
{
    global $_CONF;

    $base_path = $_CONF['path'] . 'plugins/' . $pi_name . '/';

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $base_path . 'install_defaults.php';

    return plugin_initconfig_monitor();
}

/**
 * Check runtime compatibility before installation or upgrade.
 *
 * Monitor 1.4.0 deliberately uses the common PHP subset supported by PHP 5.6
 * through PHP 8.1. Future PHP versions may work but are not claimed here until
 * tested. We reject versions older than the supported baseline and Geeklog
 * versions older than 2.1.1.
 *
 * @param string $pi_name
 * @return bool
 */
function plugin_compatible_with_this_version_monitor($pi_name)
{
    if (version_compare(PHP_VERSION, '5.6.0', '<')) {
        return false;
    }

    if (defined('VERSION') && COM_versionCompare(VERSION, '2.1.1', '<')) {
        return false;
    }

    return true;
}
