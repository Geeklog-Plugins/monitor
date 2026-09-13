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
        $html .= MONITOR_ADMIN_renderChecks(
            $checks,
            array('error', 'warning'),
            'Needs attention'
        );
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
                  . '</td><td>'
                  . (int) (isset($row['total']) ? $row['total'] : 0)
                  . '</td><td>'
                  . MONITOR_ADMIN_h(isset($row['last_seen']) ? $row['last_seen'] : '')
                  . '</td></tr>';
        }
    }

    if ($rows === 0) {
        $html .= '<tr><td colspan="3">No recent security observations.</td></tr>';
    }

    $html .= '</tbody></table></div>';

    return $html;
}

function MONITOR_ADMIN_plugins()
{
    global $_TABLES;

    $html = '<p>Monitor treats plugin updates as advice first. This view performs no installation and downloads no executable code.</p>';

    $result = DB_query(
        "SELECT pi_name, pi_version, pi_enabled, pi_gl_version, pi_homepage "
        . "FROM {$_TABLES['plugins']} ORDER BY pi_name"
    );

    $html .= '<div style="overflow:auto"><table class="admin-list" style="width:100%">'
          . '<thead><tr><th>Plugin</th><th>Installed</th><th>Code</th><th>Enabled</th><th>Geeklog requirement</th></tr></thead><tbody>';

    if ($result) {
        while ($row = DB_fetchArray($result)) {
            $name = isset($row['pi_name']) ? $row['pi_name'] : '';
            $installed = isset($row['pi_version']) ? $row['pi_version'] : '';
            $code = '';

            if ($name !== '' && function_exists('PLG_chkVersion')) {
                $codeValue = PLG_chkVersion($name);
                if (is_string($codeValue)) {
                    $code = $codeValue;
                }
            }

            $html .= '<tr><td><strong>' . MONITOR_ADMIN_h($name) . '</strong></td>'
                  . '<td>' . MONITOR_ADMIN_h($installed) . '</td>'
                  . '<td>' . MONITOR_ADMIN_h($code === '' ? 'unknown' : $code) . '</td>'
                  . '<td>' . (!empty($row['pi_enabled']) ? 'Yes' : 'No') . '</td>'
                  . '<td>' . MONITOR_ADMIN_h(isset($row['pi_gl_version']) ? $row['pi_gl_version'] : '') . '</td></tr>';
        }
    }

    $html .= '</tbody></table></div>';

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

?>