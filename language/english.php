<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin                                                            |
// +---------------------------------------------------------------------------+
// | english.php                                                               |
// |                                                                           |
// | English language file                                                     |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

global $LANG32;

$LANG_MONITOR_1 = array(
    'plugin_name'             => 'Monitor',
    'home'                    => 'Overview',
    'health'                  => 'Health',
    'security'                => 'Security',
    'view_clear_logs'         => 'Log files',
    'file'                    => 'File:',
    'log_file'                => 'Log file:',
    'view_logs'               => 'View logs',
    'clear_logs'              => 'Clear logs',
    'images_folder'           => 'Images from public_html/images folder',
    'resize'                  => 'Resize images',
    'resize_images'           => 'Resize all images',
    'resize_images_help'      => 'Monitor can identify local images larger than 1600px. Image modification should only be performed through an explicit administrator action.',
    'no_images_to_resize'     => 'There is no image bigger than 1600px',
    'change_user_photo'       => 'Change user photo',
    'comments'                => 'Comments',
    'comments_list'           => 'Comments list',
    'anonymous'               => 'Anonymous',
    'configuration'           => 'Configuration',
    'images'                  => 'Images',
    'images_list'             => 'Images list',
    'main'                    => 'Site health overview',
    'logs'                    => 'Log files',
    'updates'                 => 'Updates',
    'available_updates'       => 'Available updates from:',
    'plugin_list'             => 'Plugin updates',
    'no_update'               => 'This plugin cannot be updated by Monitor',
    'up_to_date'              => 'This plugin is already up to date',
    'update_to'               => 'Update to',
    'need_upgrade'            => 'You need to upgrade to Geeklog v',
    'before_update'           => 'before you can update to',
    'not_available'           => 'Plugin not available in this repository',
    'ask_author'              => 'This plugin does not expose compatible update information.',
    'github_limit'            => 'GitHub API requests remaining:',
    'status'                  => 'Status',
    'check'                   => 'Check',
    'value'                   => 'Value',
    'recommendation'          => 'Recommendation',
    'health_ok'               => 'OK',
    'health_info'             => 'Info',
    'health_warning'          => 'Warning',
    'health_error'            => 'Error',
    'security_observations'   => 'Security observations',
    'ban_integration'         => 'Ban integration',
    'legacy_ban_notice'       => 'Monitor 1.4.0 keeps legacy ban data readable but no longer expands its own general-purpose automatic ban engine.',
    'read_only_advice'        => 'Monitor observes and recommends by default. Changes require an explicit administrator action.'
);

// Messages for the plugin upgrade
$PLG_monitor_MESSAGE3002 = $LANG32[9];

$LANG_configsubgroups['monitor'] = array(
    'sg_main' => 'Main Settings'
);

$LANG_fs['monitor'] = array(
    'fs_main' => 'General Settings'
);

$LANG_confignames['monitor'] = array(
    'emails' => 'List of email addresses for optional Monitor notifications (comma-separated)',
    'repository' => 'GitHub repository owner used for update metadata (default: Geeklog-Plugins). Leave blank to disable remote update checks.'
);

?>
