<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;
global $LANG_configsections, $LANG_confignames, $LANG_configsubgroups, $LANG_tab, $LANG_fs;

$LANG_MONITOR_1 = array(
    'plugin_name'           => 'Monitor',
    'home'                  => 'Overview',
    'health'                => 'Health',
    'security'              => 'Security',
    'file'                  => 'File:',
    'log_file'              => 'Log file:',
    'view_logs'             => 'View logs',
    'clear_logs'            => 'Clear logs',
    'configuration'         => 'Configuration',
    'main'                  => 'Site health overview',
    'logs'                  => 'Log files',
    'updates'               => 'Plugins',
    'status'                => 'Status',
    'check'                 => 'Check',
    'value'                 => 'Value',
    'recommendation'        => 'Recommendation',
    'health_ok'             => 'OK',
    'health_info'           => 'Info',
    'health_warning'        => 'Warning',
    'health_error'          => 'Error',
    'security_observations' => 'Security observations',
    'ban_integration'       => 'Ban integration',
    'legacy_ban_notice'     => 'Monitor 1.4.0 keeps legacy ban data readable but no longer expands its own general-purpose automatic ban engine.',
    'read_only_advice'      => 'Monitor observes and recommends by default. Changes require an explicit administrator action.'
);

$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor could not complete its database migration. The installed version was not changed; review error.log and retry the upgrade.';

$GLOBALS['LANG_configsections']['monitor'] = array(
    'label' => 'Monitor',
    'title' => 'Monitor Configuration'
);

$GLOBALS['LANG_configsubgroups']['monitor'] = array(
    'sg_main' => 'Main Settings'
);

$GLOBALS['LANG_tab']['monitor'] = array(
    'tab_main' => 'Main'
);

$GLOBALS['LANG_fs']['monitor'] = array(
    'fs_main' => 'General Settings'
);

$GLOBALS['LANG_confignames']['monitor'] = array(
    'emails' => 'List of email addresses for optional Monitor notifications (comma-separated)',
    'repository' => 'GitHub repository owner reserved for plugin release metadata (default: Geeklog-Plugins). Leave blank to disable remote metadata checks.'
);

$LANG_configsections =& $GLOBALS['LANG_configsections'];
$LANG_configsubgroups =& $GLOBALS['LANG_configsubgroups'];
$LANG_tab =& $GLOBALS['LANG_tab'];
$LANG_fs =& $GLOBALS['LANG_fs'];
$LANG_confignames =& $GLOBALS['LANG_confignames'];
