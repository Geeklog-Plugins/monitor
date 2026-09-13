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
    'read_only_advice'      => 'Monitor observes and recommends by default. Changes require an explicit administrator action.',

    // Configuration audit
    'config_audit_title' => 'Configuration audit',
    'config_audit_page_title' => 'Monitor configuration audit',
    'config_audit_quick_description' => 'Check for differences between siteconfig.php and matching Core values in the database.',
    'config_audit_back' => 'Monitor overview',
    'config_audit_access_denied' => 'Access denied',
    'config_audit_root_only' => 'Access reserved for Root administrators.',
    'config_audit_intro_title' => 'Read-only configuration audit.',
    'config_audit_intro' => 'This view compares values explicitly defined in siteconfig.php with matching Core values stored in conf_values. It identifies overrides, stale database values and invalid paths without replacing Geeklog Configuration or changing settings automatically.',
    'config_audit_issues' => 'Issues',
    'config_audit_review' => 'Review',
    'config_audit_expected' => 'Expected',
    'config_audit_invalid_paths' => 'Invalid paths',
    'config_audit_active_host' => 'Active host:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'Warning:',
    'config_audit_unreadable' => 'Monitor could not read the active siteconfig.php, so the comparison is incomplete.',
    'config_audit_items_review' => 'Items to review',
    'config_audit_no_issues' => 'No configuration difference requiring attention was detected.',
    'config_audit_secondary' => 'Show %d consistent or expected value(s)',
    'config_audit_footer' => 'Sensitive values are redacted. Optional database alignment SQL is shown only for non-sensitive differences and is never executed automatically.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'Database',
    'config_audit_priority' => 'Effective source:',
    'config_audit_effective_value' => 'Effective value',
    'config_audit_why' => 'Why?',
    'config_audit_recommendation' => 'Recommendation',
    'config_audit_path' => 'Path:',
    'config_audit_path_exists' => 'exists',
    'config_audit_path_missing' => 'missing',
    'config_audit_optional_sql' => 'Optional database alignment',
    'config_audit_absent' => 'ABSENT',
    'config_audit_redacted' => '[REDACTED]',
    'config_audit_value_true' => 'true',
    'config_audit_value_false' => 'false',
    'config_audit_value_null' => 'NULL',
    'config_audit_value_object' => '[OBJECT]',

    'config_audit_level_ok' => 'Expected',
    'config_audit_level_info' => 'Information',
    'config_audit_level_review' => 'Review',
    'config_audit_level_warning' => 'Warning',

    'config_audit_status_identical' => 'Identical',
    'config_audit_status_core_file' => 'Expected file-only Core value',
    'config_audit_status_file_only' => 'Defined in siteconfig.php only',
    'config_audit_status_db_unset' => 'Database value is unset',
    'config_audit_status_different' => 'Different values',
    'config_audit_status_decode_error' => 'Database value could not be decoded',
    'config_audit_status_invalid_path' => 'Invalid path',

    'config_audit_why_identical' => 'The same value exists in siteconfig.php and conf_values.',
    'config_audit_why_core_file' => 'This Core key is expected to be defined directly in siteconfig.php.',
    'config_audit_why_file_only' => 'This key is explicitly defined in siteconfig.php and no matching Core value exists in conf_values. The siteconfig.php value is therefore used at runtime.',
    'config_audit_why_db_unset' => 'A matching Core row exists in conf_values but its stored value is unset. The siteconfig.php value remains effective.',
    'config_audit_why_different' => 'siteconfig.php and conf_values contain different values for the same Core key. Because the key is explicitly defined in siteconfig.php, that value is effective at runtime.',
    'config_audit_why_decode_error' => 'A matching Core value exists in conf_values but Monitor could not decode the stored serialized value reliably.',
    'config_audit_why_invalid_path' => 'The effective value is a filesystem path, but that path does not currently exist.',

    'config_audit_action_none' => 'No action required.',
    'config_audit_action_file_only' => 'Verify that keeping this value only in siteconfig.php is intentional. No change is required if this is expected for the site.',
    'config_audit_action_db_unset' => 'Verify that the database value is intentionally unset. The site continues to use the siteconfig.php value.',
    'config_audit_action_different' => 'Verify that this override is intentional. If the database value is obsolete, optional alignment can reduce future administrative confusion.',
    'config_audit_action_decode_error' => 'Inspect the matching Core row in conf_values before making any change.',
    'config_audit_action_invalid_path' => 'Check the configured path and filesystem availability before changing any stored value.'
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

/*
 * Early Monitor 1.4.0 development builds could persist an incomplete 2.2.x
 * configuration hierarchy. Repair it only when the Geeklog configuration UI
 * is being opened; normal frontend requests remain read-only.
 */
if (isset($_SERVER['SCRIPT_NAME'])
        && basename($_SERVER['SCRIPT_NAME']) === 'configuration.php'
        && isset($_CONF['path'])) {
    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorConfigCompat.php';
    MONITOR_repairConfiguration140();
}
