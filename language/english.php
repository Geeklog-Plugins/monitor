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

// Messages for plugin upgrade
$PLG_monitor_MESSAGE3002 = $LANG32[9];
$PLG_monitor_MESSAGE3003 = 'Monitor could not complete its database migration. The installed version was not changed; review error.log and retry the upgrade.';

$LANG_configsubgroups['monitor'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['monitor'] = array(
    'fs_main' => 'General Settings'
);

$LANG_confignames['monitor'] = array(
    'emails' => 'List of email addresses for optional Monitor notifications (comma-separated)',
    'repository' => 'GitHub repository owner reserved for plugin release metadata (default: Geeklog-Plugins). Leave blank to disable remote metadata checks.'
);
