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

    // Changes monitor
    'changes' => 'Changes',
    'changes_page_title' => 'Monitor changes',
    'changes_intro' => 'Compare lightweight snapshots of the site to see what changed between two checks. Monitor records state only; it does not modify the site.',
    'changes_capture' => 'Capture current state',
    'changes_capture_ok' => 'Current state saved.',
    'changes_capture_failed' => 'Monitor could not save the snapshot. Check that path_data is writable.',
    'changes_baseline_created' => 'Baseline created. Capture another state later to see what changed.',
    'changes_waiting' => 'A second snapshot is required before changes can be compared.',
    'changes_period' => 'Compared period:',
    'changes_previous' => 'Previous',
    'changes_current' => 'Current',
    'changes_summary_changes' => 'Changes',
    'changes_summary_plugins' => 'Plugin changes',
    'changes_summary_log' => 'New log patterns',
    'changes_summary_snapshots' => 'Snapshots',
    'changes_none' => 'No meaningful change was detected between these two snapshots.',
    'changes_detected' => 'Detected changes',
    'changes_environment' => 'Environment',
    'changes_plugins' => 'Plugins',
    'changes_storage' => 'Storage',
    'changes_logs' => 'New error.log activity',
    'changes_before' => 'Before',
    'changes_after' => 'After',
    'changes_occurrences' => 'occurrence(s)',
    'changes_log_none' => 'No new error, warning or exception pattern was detected in error.log for this period.',
    'changes_log_rotated' => 'error.log was rotated or truncated between the two snapshots, so the new portion cannot be compared reliably.',
    'changes_log_truncated' => 'The new log data exceeded the analysis limit. Monitor analyzed only the most recent 512 KiB.',
    'changes_current_state' => 'Current state',
    'changes_geeklog' => 'Geeklog',
    'changes_php' => 'PHP',
    'changes_plugins_count' => 'Installed plugins',
    'changes_disk_free' => 'Free disk space',
    'changes_error_log_size' => 'error.log size',
    'changes_unknown' => 'Unknown',
    'changes_enabled' => 'enabled',
    'changes_disabled' => 'disabled',
    'changes_code_geeklog_changed' => 'Geeklog version changed',
    'changes_code_php_changed' => 'PHP version changed',
    'changes_code_plugin_installed' => 'Plugin installed',
    'changes_code_plugin_removed' => 'Plugin removed',
    'changes_code_plugin_version_changed' => 'Plugin version changed',
    'changes_code_plugin_enabled' => 'Plugin enabled',
    'changes_code_plugin_disabled' => 'Plugin disabled',
    'changes_code_disk_free_decreased' => 'Free disk space decreased',

    // Configuration audit
    'config_audit_title' => 'Configuration audit',
    'config_audit_page_title' => 'Monitor configuration audit',
    'config_audit_quick_description' => 'Check for differences between siteconfig.php and matching Core values in the database.',
    'config_audit_back' => 'Monitor overview',
    'config_audit_access_denied' => 'Access denied',
    'config_audit_root_only' => 'Access reserved for Root administrators.',
    'config_audit_intro_title' => 'Read-only configuration audit.',
    'config_audit_intro' => 'Compares Core values explicitly defined in siteconfig.php with matching values in conf_values. Only differences or invalid values require attention.',
    'config_audit_issues' => 'Issues',
    'config_audit_review' => 'Review',
    'config_audit_normal' => 'Normal',
    'config_audit_active_host' => 'Host:',
    'config_audit_siteconfig' => 'siteconfig.php:',
    'config_audit_unreadable_title' => 'Warning:',
    'config_audit_unreadable' => 'Monitor could not read the active siteconfig.php, so the comparison is incomplete.',
    'config_audit_items_review' => 'Needs attention',
    'config_audit_no_issues' => 'Configuration is consistent. No conflicting Core value requires attention.',
    'config_audit_secondary' => 'Show %d normal or informational value(s)',
    'config_audit_footer' => 'Sensitive values are redacted. Optional database alignment SQL is never executed automatically.',
    'config_audit_source_siteconfig' => 'siteconfig.php',
    'config_audit_source_database' => 'Database',
    'config_audit_priority' => 'Effective source:',
    'config_audit_effective_value' => 'Effective value',
    'config_audit_details' => 'Details',
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
    'config_audit_status_core_file' => 'Expected in siteconfig.php',
    'config_audit_status_file_only' => 'siteconfig.php only',
    'config_audit_status_db_unset' => 'Database value unset',
    'config_audit_status_different' => 'Different values',
    'config_audit_status_decode_error' => 'Database value unreadable',
    'config_audit_status_invalid_path' => 'Invalid path',

    'config_audit_why_identical' => 'The same value exists in siteconfig.php and conf_values.',
    'config_audit_why_core_file' => 'This Core key is normally defined directly in siteconfig.php.',
    'config_audit_why_file_only' => 'No matching Core value exists in conf_values. The siteconfig.php value is used.',
    'config_audit_why_db_unset' => 'A matching Core row exists in conf_values but its value is unset. siteconfig.php remains effective.',
    'config_audit_why_different' => 'siteconfig.php and conf_values contain different values. siteconfig.php is effective at runtime.',
    'config_audit_why_decode_error' => 'The matching Core value in conf_values could not be decoded reliably.',
    'config_audit_why_invalid_path' => 'The effective filesystem path does not currently exist.',

    'config_audit_action_none' => 'No action required.',
    'config_audit_action_file_only' => 'No action required unless this value should also be managed in the database.',
    'config_audit_action_db_unset' => 'Verify that the database value is intentionally unset.',
    'config_audit_action_different' => 'Verify that this override is intentional. Align the database value only if the stored value is obsolete.',
    'config_audit_action_decode_error' => 'Inspect the matching Core row in conf_values before making any change.',
    'config_audit_action_invalid_path' => 'Check the configured path and filesystem availability.',

    // Plugin catalog
    'plugin_catalog_intro' => 'Installed plugins are compared with public repositories from the configured GitHub owner. Monitor reports available versions and discovery candidates but never installs or updates code.',
    'plugin_catalog_owner' => 'GitHub source:',
    'plugin_catalog_refresh' => 'Refresh GitHub data',
    'plugin_catalog_installed' => 'Installed plugins',
    'plugin_catalog_discover' => 'Discover plugins',
    'plugin_catalog_discover_intro' => 'Recent public repositories not installed on this site. Review compatibility and documentation before installing anything.',
    'plugin_catalog_legacy_discover' => 'Older repositories',
    'plugin_catalog_legacy_intro' => 'Older public repositories may still be useful, but their recent Geeklog and PHP compatibility is unknown.',
    'plugin_catalog_plugin' => 'Plugin',
    'plugin_catalog_installed_version' => 'Installed',
    'plugin_catalog_code_version' => 'Code',
    'plugin_catalog_latest_release' => 'Latest release',
    'plugin_catalog_latest_version' => 'Latest GitHub version',
    'plugin_catalog_version_source_release' => 'Release',
    'plugin_catalog_version_source_tag' => 'Tag',
    'plugin_catalog_state' => 'State',
    'plugin_catalog_enabled' => 'Enabled',
    'plugin_catalog_geeklog' => 'Geeklog requirement',
    'plugin_catalog_repository' => 'Repository',
    'plugin_catalog_yes' => 'Yes',
    'plugin_catalog_no' => 'No',
    'plugin_catalog_unknown' => 'Unknown',
    'plugin_catalog_no_release' => 'No release metadata',
    'plugin_catalog_no_version' => 'No release or version tag found',
    'plugin_catalog_no_repository' => 'No matching repository',
    'plugin_catalog_current' => 'Up to date',
    'plugin_catalog_update' => 'Update available',
    'plugin_catalog_ahead' => 'Installed version newer',
    'plugin_catalog_remote_unavailable' => 'GitHub metadata is unavailable. Local plugin information is still shown.',
    'plugin_catalog_remote_disabled' => 'Remote metadata checks are disabled because no valid GitHub owner is configured.',
    'plugin_catalog_none_discoverable' => 'No additional recent plugin repository was found for this GitHub owner.',
    'plugin_catalog_open_repository' => 'Open repository',
    'plugin_catalog_open_release' => 'Open release',
    'plugin_catalog_open_version' => 'Open version',
    'plugin_catalog_updated' => 'Updated',
    'plugin_catalog_summary_installed' => 'Installed',
    'plugin_catalog_summary_updates' => 'Updates available',
    'plugin_catalog_summary_discover' => 'Recent candidates',
    'plugin_catalog_summary_unmatched' => 'Without GitHub match'
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
