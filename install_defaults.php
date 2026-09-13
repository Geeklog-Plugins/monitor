<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// +---------------------------------------------------------------------------+

/**
 * Initial configuration defaults.
 *
 * These values are used for fresh installation and as migration fallbacks only.
 * Runtime code reads the persisted Geeklog configuration.
 *
 * @package Monitor
 */

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

global $_monitor_DEFAULT;

$plugin_path = $_CONF['path'] . 'plugins/monitor/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

$_monitor_DEFAULT = array(
    'emails' => $_CONF['site_mail'],
    'repository' => 'Geeklog-Plugins'
);

/**
 * Initialize Monitor configuration for a fresh installation.
 *
 * @return bool
 */
function plugin_initconfig_monitor()
{
    global $_monitor_DEFAULT;

    $c = config::get_instance();
    if ($c->group_exists('monitor')) {
        return true;
    }

    $c->add('sg_main', null, 'subgroup', 0, 0, null, 0, true, 'monitor');
    $c->add('fs_main', null, 'fieldset', 0, 0, null, 0, true, 'monitor');
    $c->add(
        'emails',
        $_monitor_DEFAULT['emails'],
        'text',
        0,
        0,
        0,
        10,
        true,
        'monitor'
    );
    $c->add(
        'repository',
        $_monitor_DEFAULT['repository'],
        'text',
        0,
        0,
        0,
        20,
        true,
        'monitor'
    );

    return true;
}
