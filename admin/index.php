<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/index.php                                                           |
// |                                                                           |
// | Focused health, diagnostics, security and plugin state dashboard.         |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorBanAdapter.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorHealth.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorPluginCatalog.php';

if (!SEC_hasRights('monitor.admin')) {
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    $username = isset($_USER['username']) ? $_USER['username'] : 'unknown';
    COM_accessLog(
        'User ' . $username
        . ' tried to illegally access the Monitor administration screen.'
    );
    COM_output(COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30])));
    exit;
}

function MONITOR_ADMIN_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function MONITOR_ADMIN_statusLabel($status)
{
    global $LANG_MONITOR_1;

    $map = array(
        'ok' => $LANG_MONITOR_1['health_ok'],
        'info' => $LANG_MONITOR_1['health_info'],
        'warning' => $LANG_MONITOR_1['health_warning'],
        'error' => $LANG_MONITOR_1['health_error']
    );
    $styles = array(
        'ok' => 'background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;',
        'info' => 'background:#e3f2fd;color:#0d47a1;border:1px solid #90caf9;',
        'warning' => 'background:#fff8e1;color:#7a4f00;border:1px solid #ffe082;',
        'error' => 'background:#ffebee;color:#b71c1c;border:1px solid #ef9a9a;'
    );

    if (!isset($map[$status])) {
        $status = 'info';
    }

    return '<span style="display:inline-block;padding:3px 8px;border-radius:12px;font-weight:bold;'
         . $styles[$status] . '">'
         . MONITOR_ADMIN_h($map[$status])
         . '</span>';
}

function MONITOR_ADMIN_navigation($active)
{
    global $_CONF, $LANG_MONITOR_1;

    $base = $_CONF['site_admin_url'] . '/plugins/monitor/index.php';
    $items = array(
        'overview' => $LANG_MONITOR_1['home'],
        'security' => $LANG_MONITOR_1['security'],
        'plugins' => $LANG_MONITOR_1['updates']
    );

    $html = '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0 0 18px 0">';

    foreach ($items as $key => $label) {
        $url = $base . '?view=' . rawurlencode($key);
        $style = 'display:inline-block;padding:7px 11px;border:1px solid #c7ccd1;border-radius:5px;text-decoration:none;';
        if ($key === $active) {
            $style .= 'font-weight:bold;background:#eef2f5;';
        }

        $html .= '<a style="' . $style . '" href="' . MONITOR_ADMIN_h($url) . '">'
              . MONITOR_ADMIN_h($label) . '</a>';
    }

    $html .= '<a style="display:inline-block;padding:7px 11px;border:1px solid #c7ccd1;border-radius:5px;text-decoration:none" href="'
          . MONITOR_ADMIN_h($_CONF['site_admin_url'] . '/logviewer.php')
          . '">Geeklog logs</a>';

    $html .= '<form style="display:inline" action="'
          . MONITOR_ADMIN_h($_CONF['site_admin_url'] . '/configuration.php')
          . '" method="post">'
          . '<input type="hidden" name="conf_group" value="monitor">'
          . '<button type="submit" style="padding:7px 11px">'
          . MONITOR_ADMIN_h($LANG_MONITOR_1['configuration'])
          . '</button></form>';

    $html .= '</div>';

    return $html;
}

function MONITOR_ADMIN_summaryCard($label, $value, $kind)
{
    $border = '#cfd8dc';
    $background = '#ffffff';

    if ($kind === 'error') {
        $border = '#ef9a9a';
        $background = '#fff5f5';
    } elseif ($kind === 'warning') {
        $border = '#ffe082';
        $background = '#fffaf0';
    } elseif ($kind === 'ok') {
        $border = '#a5d6a7';
        $background = '#f4fbf5';
    } elseif ($kind === 'info') {
        $border = '#90caf9';
        $background = '#f5faff';
    }

    return '<div style="min-width:120px;flex:1;padding:14px;border:1px solid ' . $border
         . ';background:' . $background . ';border-radius:7px">'
         . '<div style="font-size:1.55em;font-weight:bold;line-height:1.1">' . (int) $value . '</div>'
         . '<div style="margin-top:5px">' . MONITOR_ADMIN_h($label) . '</div>'
         . '</div>';
}

function MONITOR_ADMIN_renderChecks($checks, $statuses, $title)
{
    global $LANG_MONITOR_1;

    $rows = array();
    foreach ($checks as $check) {
        if (in_array($check['status'], $statuses, true)) {
            $rows[] = $check;
        }
    }

    if (empty($rows)) {
        return '';
    }

    $html = '<h3 style="margin-top:24px">' . MONITOR_ADMIN_h($title) . '</h3>';
    $html .= '<div style="overflow:auto"><table class="admin-list" style="width:100%;border-collapse:collapse">'
          . '<thead><tr>'
          . '<th style="width:90px">' . MONITOR_ADMIN_h($LANG_MONITOR_1['status']) . '</th>'
          . '<th>' . MONITOR_ADMIN_h($LANG_MONITOR_1['check']) . '</th>'
          . '<th>' . MONITOR_ADMIN_h($LANG_MONITOR_1['value']) . '</th>'
          . '<th>' . MONITOR_ADMIN_h($LANG_MONITOR_1['recommendation']) . '</th>'
          . '</tr></thead><tbody>';

    foreach ($rows as $check) {
        $html .= '<tr>'
              . '<td>' . MONITOR_ADMIN_statusLabel($check['status']) . '</td>'
              . '<td><strong>' . MONITOR_ADMIN_h($check['label']) . '</strong></td>'
              . '<td><code>' . MONITOR_ADMIN_h($check['value']) . '</code></td>'
              . '<td>' . MONITOR_ADMIN_h($check['recommendation']) . '</td>'
              . '</tr>';
    }

    $html .= '</tbody></table></div>';

    return $html;
}

function MONITOR_ADMIN_overview()
{
    global $_CONF, $LANG_MONITOR_1;

    $checks = MONITOR_HEALTH_collect();
    $summary = MONITOR_HEALTH_summary($checks);
    $needsAttention = (int) $summary['error'] + (int) $summary['warning'];

    $html = '<div style="padding:16px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:18px">';
    $html .= '<div style="font-size:1.15em;font-weight:bold;margin-bottom:5px">Site health at a glance</div>';

    if ($summary['error'] > 0) {
        $html .= '<div>Monitor detected <strong>' . (int) $summary['error'] . ' error(s)</strong> requiring attention.</div>';
    } elseif ($summary['warning'] > 0) {
        $html .= '<div>No critical error, but <strong>' . (int) $summary['warning'] . ' warning(s)</strong> should be reviewed.</div>';
    } else {
        $html .= '<div>No current error or warning was detected by the available Monitor checks.</div>';
    }

    $html .= '<div style="margin-top:7px;color:#555">'
          . MONITOR_ADMIN_h($LANG_MONITOR_1['read_only_advice'])
          . '</div></div>';

    $html .= '<div style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:22px">'
          . MONITOR_ADMIN_summaryCard('Errors', $summary['error'], 'error')
          . MONITOR_ADMIN_summaryCard('Warnings', $summary['warning'], 'warning')
          . MONITOR_ADMIN_summaryCard('Information', $summary['info'], 'info')
          . MONITOR_ADMIN_summaryCard('OK', $summary['ok'], 'ok')
          . '</div>';

    $html .= '<h3>Quick actions</h3>';
    $html .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(210px,1fr));gap:10px;margin-bottom:22px">';

    $actions = array(
        array('Geeklog logs', $_CONF['site_admin_url'] . '/logviewer.php', 'Use the native Geeklog log viewer for complete log access.'),
        array('Security', $_CONF['site_admin_url'] . '/plugins/monitor/index.php?view=security', 'Review Monitor security observations and Ban integration.'),
        array('Plugins', $_CONF['site_admin_url'] . '/plugins/monitor/index.php?view=plugins', 'Review installed plugin versions and compatibility state.')
    );

    if (SEC_inGroup('Root')) {
        $actions[] = array(
            'Configuration audit',
            $_CONF['site_admin_url'] . '/plugins/monitor/config-audit.php',
            'Check for differences between siteconfig.php and matching Core values in the database.'
        );
    }

    foreach ($actions as $action) {
        $html .= '<a href="' . MONITOR_ADMIN_h($action[1]) . '" style="display:block;padding:13px;border:1px solid #d7dde2;border-radius:7px;text-decoration:none">'
              . '<strong>' . MONITOR_ADMIN_h($action[0]) . '</strong>'
              . '<div style="margin-top:5px;color:#555;font-size:.95em">'
              . MONITOR_ADMIN_h($action[2])
              . '</div></a>';
    }

    $html .= '</div>';

    if ($needsAttention > 0) {
        $html .= MONITOR_ADMIN_renderChecks($checks, array('error', 'warning'), 'Needs attention');
    }

    $html .= MONITOR_ADMIN_renderChecks(
        $checks,
        array('ok', 'info'),
        $needsAttention > 0 ? 'Other diagnostics' : 'Diagnostics'
    );

    return $html;
}

function MONITOR_ADMIN_security()
{
    global $_TABLES, $LANG_MONITOR_1;

    $html = '<p>' . MONITOR_ADMIN_h($LANG_MONITOR_1['legacy_ban_notice']) . '</p>';
    $capabilities = MONITOR_BAN_capabilities();
    $version = MONITOR_BAN_version();

    $html .= '<h3>' . MONITOR_ADMIN_h($LANG_MONITOR_1['ban_integration']) . '</h3>';
    $html .= '<div style="overflow:auto"><table class="admin-list">'
          . '<tr><th>Installed/enabled</th><td>' . (!empty($capabilities['installed']) ? 'Yes' : 'No') . '</td></tr>'
          . '<tr><th>Version</th><td>' . MONITOR_ADMIN_h($version === '' ? 'unknown' : $version) . '</td></tr>'
          . '<tr><th>IP ban request capability</th><td>' . (!empty($capabilities['request_ip_ban']) ? 'Available' : 'Unavailable') . '</td></tr>'
          . '<tr><th>Direct Ban SQL coupling</th><td>No</td></tr>'
          . '</table></div>';

    $html .= '<h3>' . MONITOR_ADMIN_h($LANG_MONITOR_1['security_observations']) . '</h3>';

    if (!DB_checkTableExists('monitor_ban')) {
        return $html . '<p>No legacy Monitor security table is present.</p>';
    }

    $result = DB_query(
        "SELECT bantype, COUNT(*) AS total, MAX(created) AS last_seen "
        . "FROM {$_TABLES['monitor_ban']} GROUP BY bantype ORDER BY bantype",
        1
    );

    $html .= '<div style="overflow:auto"><table class="admin-list" style="width:100%">'
          . '<thead><tr><th>Type</th><th>Count</th><th>Last seen</th></tr></thead><tbody>';

    $rows = 0;
    if ($result) {
        while ($row = DB_fetchArray($result)) {
            $rows++;
            $html .= '<tr><td>'
                  . MONITOR_ADMIN_h(isset($row['bantype']) ? $row['bantype'] : '')
                  . '</td><td>' . (int) (isset($row['total']) ? $row['total'] : 0)
                  . '</td><td>' . MONITOR_ADMIN_h(isset($row['last_seen']) ? $row['last_seen'] : '')
                  . '</td></tr>';
        }
    }

    if ($rows === 0) {
        $html .= '<tr><td colspan="3">No recent security observations.</td></tr>';
    }

    $html .= '</tbody></table></div>';

    return $html;
}

function MONITOR_ADMIN_pluginBadge($state)
{
    global $LANG_MONITOR_1;

    $labels = array(
        'current' => $LANG_MONITOR_1['plugin_catalog_current'],
        'update' => $LANG_MONITOR_1['plugin_catalog_update'],
        'ahead' => $LANG_MONITOR_1['plugin_catalog_ahead'],
        'unknown' => $LANG_MONITOR_1['plugin_catalog_unknown'],
        'no_version' => $LANG_MONITOR_1['plugin_catalog_no_version'],
        'no_repository' => $LANG_MONITOR_1['plugin_catalog_no_repository']
    );
    $styles = array(
        'current' => 'background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;',
        'update' => 'background:#fff3e0;color:#8a4300;border:1px solid #ffcc80;',
        'ahead' => 'background:#e3f2fd;color:#0d47a1;border:1px solid #90caf9;',
        'unknown' => 'background:#f5f5f5;color:#555;border:1px solid #d7dde2;',
        'no_version' => 'background:#f5f5f5;color:#555;border:1px solid #d7dde2;',
        'no_repository' => 'background:#f5f5f5;color:#555;border:1px solid #d7dde2;'
    );

    if (!isset($labels[$state])) {
        $state = 'unknown';
    }

    return '<span style="display:inline-block;padding:3px 8px;border-radius:12px;font-weight:bold;font-size:.9em;'
         . $styles[$state] . '">'
         . MONITOR_ADMIN_h($labels[$state]) . '</span>';
}

function MONITOR_ADMIN_pluginCard($plugin)
{
    global $LANG_MONITOR_1;

    $html = '<section style="border:1px solid #d7dde2;border-radius:8px;padding:13px;background:#fff">';
    $html .= '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;justify-content:space-between">'
          . '<strong style="font-size:1.08em">' . MONITOR_ADMIN_h($plugin['name']) . '</strong>'
          . MONITOR_ADMIN_pluginBadge($plugin['state']) . '</div>';

    $html .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(125px,1fr));gap:8px;margin-top:11px;font-size:.93em">'
          . '<div><span style="color:#666">' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_installed_version']) . '</span><br><strong>'
          . MONITOR_ADMIN_h($plugin['installed']) . '</strong></div>'
          . '<div><span style="color:#666">' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_latest_version']) . '</span><br><strong>'
          . MONITOR_ADMIN_h($plugin['remote_label']) . '</strong></div>'
          . '<div><span style="color:#666">' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_enabled']) . '</span><br><strong>'
          . MONITOR_ADMIN_h($plugin['enabled']) . '</strong></div>'
          . '<div><span style="color:#666">' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_geeklog']) . '</span><br><strong>'
          . MONITOR_ADMIN_h($plugin['gl_version']) . '</strong></div>'
          . '</div>';

    if ($plugin['repository_url'] !== '') {
        $html .= '<div style="margin-top:10px;display:flex;flex-wrap:wrap;gap:12px;font-size:.92em">'
              . '<a href="' . MONITOR_ADMIN_h($plugin['repository_url']) . '" target="_blank" rel="noopener noreferrer">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_open_repository']) . '</a>';
        if ($plugin['remote_url'] !== '') {
            $html .= '<a href="' . MONITOR_ADMIN_h($plugin['remote_url']) . '" target="_blank" rel="noopener noreferrer">'
                  . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_open_version']) . '</a>';
        }
        $html .= '</div>';
    }

    $html .= '</section>';

    return $html;
}

function MONITOR_ADMIN_discoveryCard($repo)
{
    global $LANG_MONITOR_1;

    $html = '<section style="border:1px solid #d7dde2;border-radius:8px;padding:12px;background:#fff">'
          . '<strong>' . MONITOR_ADMIN_h($repo['name']) . '</strong>';

    if (!empty($repo['description'])) {
        $html .= '<div style="margin-top:6px;color:#555;font-size:.93em">'
              . MONITOR_ADMIN_h($repo['description']) . '</div>';
    }
    if (!empty($repo['updated_at'])) {
        $html .= '<div style="margin-top:8px;font-size:.88em;color:#666">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_updated']) . ': '
              . MONITOR_ADMIN_h(substr($repo['updated_at'], 0, 10)) . '</div>';
    }
    if (!empty($repo['url'])) {
        $html .= '<div style="margin-top:8px"><a href="' . MONITOR_ADMIN_h($repo['url'])
              . '" target="_blank" rel="noopener noreferrer">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_open_repository']) . '</a></div>';
    }

    return $html . '</section>';
}

function MONITOR_ADMIN_plugins()
{
    global $_TABLES, $_CONF, $LANG_MONITOR_1;

    $refresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';
    $catalog = MONITOR_PLUGIN_CATALOG_repositories($refresh);
    $owner = isset($catalog['owner']) ? $catalog['owner'] : '';
    $repositories = isset($catalog['repositories']) && is_array($catalog['repositories'])
        ? $catalog['repositories'] : array();

    $installedPlugins = array();
    $installedNames = array();
    $updatesAvailable = 0;
    $withoutRepository = 0;

    $result = DB_query(
        "SELECT pi_name, pi_version, pi_enabled, pi_gl_version, pi_homepage "
        . "FROM {$_TABLES['plugins']} ORDER BY pi_name"
    );

    if ($result) {
        while ($row = DB_fetchArray($result)) {
            $name = isset($row['pi_name']) ? (string) $row['pi_name'] : '';
            if ($name === '') {
                continue;
            }

            $installed = isset($row['pi_version']) ? (string) $row['pi_version'] : '';
            $repo = MONITOR_PLUGIN_CATALOG_matchRepository($name, $repositories);
            $state = 'no_repository';
            $repositoryUrl = '';
            $remoteUrl = '';
            $remoteLabel = $LANG_MONITOR_1['plugin_catalog_no_repository'];

            if (is_array($repo)) {
                $repositoryUrl = isset($repo['url']) ? $repo['url'] : '';
                $remote = !empty($catalog['available'])
                    ? MONITOR_PLUGIN_CATALOG_latestVersion(
                        $owner,
                        isset($repo['name']) ? $repo['name'] : '',
                        $refresh
                    )
                    : null;

                if (is_array($remote)) {
                    $remoteLabel = isset($remote['tag']) ? $remote['tag'] : '';
                    $remoteUrl = isset($remote['url']) ? $remote['url'] : '';
                    $state = MONITOR_PLUGIN_CATALOG_versionState(
                        $installed,
                        isset($remote['version']) ? $remote['version'] : ''
                    );
                    if ($state === 'update') {
                        $updatesAvailable++;
                    }
                } else {
                    $remoteLabel = $LANG_MONITOR_1['plugin_catalog_no_version'];
                    $state = 'no_version';
                }
            } else {
                $withoutRepository++;
            }

            $installedNames[MONITOR_PLUGIN_CATALOG_normalizeName($name)] = true;
            $installedPlugins[] = array(
                'name' => $name,
                'installed' => $installed === '' ? $LANG_MONITOR_1['plugin_catalog_unknown'] : $installed,
                'enabled' => !empty($row['pi_enabled'])
                    ? $LANG_MONITOR_1['plugin_catalog_yes'] : $LANG_MONITOR_1['plugin_catalog_no'],
                'gl_version' => !empty($row['pi_gl_version'])
                    ? $row['pi_gl_version'] : $LANG_MONITOR_1['plugin_catalog_unknown'],
                'remote_label' => $remoteLabel,
                'state' => $state,
                'repository_url' => $repositoryUrl,
                'remote_url' => $remoteUrl
            );
        }
    }

    $recent = array();
    $legacy = array();
    foreach ($repositories as $repo) {
        if (!MONITOR_PLUGIN_CATALOG_isDiscoverable($repo)) {
            continue;
        }

        $normalized = MONITOR_PLUGIN_CATALOG_normalizeName($repo['name']);
        if ($normalized !== '' && isset($installedNames[$normalized])) {
            continue;
        }

        if (MONITOR_PLUGIN_CATALOG_discoveryState($repo) === 'active') {
            $recent[] = $repo;
        } else {
            $legacy[] = $repo;
        }
    }

    $sortRepos = function ($a, $b) {
        return strcmp($b['updated_at'], $a['updated_at']);
    };
    usort($recent, $sortRepos);
    usort($legacy, $sortRepos);

    $html = '<div style="padding:13px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:16px">'
          . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_intro']);

    if ($owner !== '') {
        $ownerUrl = 'https://github.com/' . rawurlencode($owner);
        $html .= '<div style="margin-top:7px;font-size:.93em">'
              . '<strong>' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_owner']) . '</strong> '
              . '<a href="' . MONITOR_ADMIN_h($ownerUrl) . '" target="_blank" rel="noopener noreferrer">'
              . MONITOR_ADMIN_h($owner) . '</a>'
              . ' &nbsp; <a href="' . MONITOR_ADMIN_h($_CONF['site_admin_url'] . '/plugins/monitor/index.php?view=plugins&refresh=1') . '">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_refresh']) . '</a></div>';
    }
    $html .= '</div>';

    if ($owner === '') {
        $html .= '<div style="padding:11px;border:1px solid #ffe082;background:#fffaf0;border-radius:7px;margin-bottom:16px">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_remote_disabled']) . '</div>';
    } elseif (empty($catalog['available'])) {
        $html .= '<div style="padding:11px;border:1px solid #ffe082;background:#fffaf0;border-radius:7px;margin-bottom:16px">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_remote_unavailable']) . '</div>';
    }

    $html .= '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px">'
          . MONITOR_ADMIN_summaryCard($LANG_MONITOR_1['plugin_catalog_summary_installed'], count($installedPlugins), 'info')
          . MONITOR_ADMIN_summaryCard($LANG_MONITOR_1['plugin_catalog_summary_updates'], $updatesAvailable, $updatesAvailable > 0 ? 'warning' : 'ok')
          . MONITOR_ADMIN_summaryCard($LANG_MONITOR_1['plugin_catalog_summary_discover'], count($recent), 'info')
          . MONITOR_ADMIN_summaryCard($LANG_MONITOR_1['plugin_catalog_summary_unmatched'], $withoutRepository, 'info')
          . '</div>';

    $html .= '<h3>' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_installed']) . '</h3>';
    $html .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:10px;margin-bottom:24px">';
    foreach ($installedPlugins as $plugin) {
        $html .= MONITOR_ADMIN_pluginCard($plugin);
    }
    $html .= '</div>';

    $html .= '<h3>' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_discover']) . '</h3>';
    $html .= '<p style="color:#555">' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_discover_intro']) . '</p>';

    if (empty($recent)) {
        $html .= '<p>' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_none_discoverable']) . '</p>';
    } else {
        $html .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:10px">';
        foreach ($recent as $repo) {
            $html .= MONITOR_ADMIN_discoveryCard($repo);
        }
        $html .= '</div>';
    }

    if (!empty($legacy)) {
        $html .= '<details style="margin-top:20px"><summary style="cursor:pointer;font-weight:bold">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_legacy_discover'])
              . ' (' . count($legacy) . ')</summary>'
              . '<p style="color:#555">' . MONITOR_ADMIN_h($LANG_MONITOR_1['plugin_catalog_legacy_intro']) . '</p>'
              . '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:10px">';
        foreach ($legacy as $repo) {
            $html .= MONITOR_ADMIN_discoveryCard($repo);
        }
        $html .= '</div></details>';
    }

    return $html;
}

$requestedView = isset($_GET['view']) ? COM_applyFilter($_GET['view']) : 'overview';
$allowedViews = array('overview', 'security', 'plugins');
if (!in_array($requestedView, $allowedViews, true)) {
    $requestedView = 'overview';
}

$content = MONITOR_ADMIN_navigation($requestedView);

switch ($requestedView) {
    case 'security':
        $title = $LANG_MONITOR_1['security'];
        $content .= MONITOR_ADMIN_security();
        break;

    case 'plugins':
        $title = $LANG_MONITOR_1['updates'];
        $content .= MONITOR_ADMIN_plugins();
        break;

    case 'overview':
    default:
        $title = $LANG_MONITOR_1['main'];
        $content .= MONITOR_ADMIN_overview();
        break;
}

$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file(array('admin' => 'administration.thtml'));
$T->set_var(array(
    'title' => MONITOR_ADMIN_h($title),
    'status_msg' => '',
    'admin_body' => $content
));
$T->parse('output', 'admin');

$body = $T->finish($T->get_var('output'));
$display = COM_createHTMLDocument($body, array('pagetitle' => $title));
COM_output($display);
