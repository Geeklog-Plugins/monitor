<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/index.php                                                           |
// |                                                                           |
// | Focused health, diagnostics, logs, security and plugin state dashboard.   |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorBanAdapter.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorHealth.php';

// Ensure the user has rights to access every Monitor administration view.
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

/**
 * Escape a value for admin HTML output.
 *
 * @param string $value
 * @return string
 */
function MONITOR_ADMIN_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Return the list of regular files in Geeklog's configured log directory.
 *
 * @return array
 */
function MONITOR_ADMIN_logFiles()
{
    global $_CONF;

    $files = array();
    if (!isset($_CONF['path_log']) || !is_dir($_CONF['path_log'])) {
        return $files;
    }

    $entries = scandir($_CONF['path_log']);
    if ($entries === false) {
        return $files;
    }

    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $path = $_CONF['path_log'] . $entry;
        if (is_file($path)) {
            $files[] = $entry;
        }
    }

    natcasesort($files);

    return array_values($files);
}

/**
 * Verify a requested log filename belongs to the configured log directory.
 *
 * @param string $filename
 * @param array  $allowed
 * @return string
 */
function MONITOR_ADMIN_validLog($filename, $allowed)
{
    $filename = basename((string) $filename);

    return in_array($filename, $allowed, true) ? $filename : '';
}

/**
 * Render dashboard navigation.
 *
 * @param string $active
 * @return string
 */
function MONITOR_ADMIN_navigation($active)
{
    global $_CONF, $LANG_MONITOR_1;

    $base = $_CONF['site_admin_url'] . '/plugins/monitor/index.php';
    $items = array(
        'overview' => $LANG_MONITOR_1['home'],
        'logs' => $LANG_MONITOR_1['logs'],
        'security' => $LANG_MONITOR_1['security'],
        'plugins' => $LANG_MONITOR_1['updates']
    );

    $html = '<p class="monitor-nav">';
    $first = true;

    foreach ($items as $key => $label) {
        if (!$first) {
            $html .= ' | ';
        }
        $first = false;

        $url = $base . '?view=' . rawurlencode($key);
        if ($key === $active) {
            $html .= '<strong>' . MONITOR_ADMIN_h($label) . '</strong>';
        } else {
            $html .= COM_createLink(MONITOR_ADMIN_h($label), $url);
        }
    }

    $html .= ' | ';
    $html .= '<form style="display:inline" action="'
          . MONITOR_ADMIN_h($_CONF['site_admin_url'] . '/configuration.php')
          . '" method="post">'
          . '<input type="hidden" name="conf_group" value="monitor">'
          . '<button type="submit">'
          . MONITOR_ADMIN_h($LANG_MONITOR_1['configuration'])
          . '</button></form>';
    $html .= '</p>';

    return $html;
}

/**
 * Render one health status label.
 *
 * @param string $status
 * @return string
 */
function MONITOR_ADMIN_statusLabel($status)
{
    global $LANG_MONITOR_1;

    $map = array(
        'ok' => $LANG_MONITOR_1['health_ok'],
        'info' => $LANG_MONITOR_1['health_info'],
        'warning' => $LANG_MONITOR_1['health_warning'],
        'error' => $LANG_MONITOR_1['health_error']
    );

    if (!isset($map[$status])) {
        $status = 'info';
    }

    return '<strong class="monitor-status monitor-status-'
         . MONITOR_ADMIN_h($status) . '">'
         . MONITOR_ADMIN_h($map[$status])
         . '</strong>';
}

/**
 * Render the health overview.
 *
 * @return string
 */
function MONITOR_ADMIN_overview()
{
    global $LANG_MONITOR_1;

    $checks = MONITOR_HEALTH_collect();
    $summary = MONITOR_HEALTH_summary($checks);

    $html = '<p>' . MONITOR_ADMIN_h($LANG_MONITOR_1['read_only_advice']) . '</p>';
    $html .= '<p><strong>'
          . (int) $summary['error'] . ' error(s), '
          . (int) $summary['warning'] . ' warning(s), '
          . (int) $summary['info'] . ' info, '
          . (int) $summary['ok'] . ' OK'
          . '</strong></p>';

    $html .= '<table class="admin-list" style="width:100%">'
          . '<thead><tr>'
          . '<th>' . MONITOR_ADMIN_h($LANG_MONITOR_1['status']) . '</th>'
          . '<th>' . MONITOR_ADMIN_h($LANG_MONITOR_1['check']) . '</th>'
          . '<th>' . MONITOR_ADMIN_h($LANG_MONITOR_1['value']) . '</th>'
          . '<th>' . MONITOR_ADMIN_h($LANG_MONITOR_1['recommendation']) . '</th>'
          . '</tr></thead><tbody>';

    foreach ($checks as $check) {
        $html .= '<tr>'
              . '<td>' . MONITOR_ADMIN_statusLabel($check['status']) . '</td>'
              . '<td>' . MONITOR_ADMIN_h($check['label']) . '</td>'
              . '<td><code>' . MONITOR_ADMIN_h($check['value']) . '</code></td>'
              . '<td>' . MONITOR_ADMIN_h($check['recommendation']) . '</td>'
              . '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}

/**
 * Render the safe log viewer.
 *
 * @return string
 */
function MONITOR_ADMIN_logs()
{
    global $_CONF, $LANG_MONITOR_1;

    $files = MONITOR_ADMIN_logFiles();
    $selected = isset($_GET['log'])
        ? MONITOR_ADMIN_validLog($_GET['log'], $files)
        : '';

    if ($selected === '' && !empty($files)) {
        $selected = $files[0];
    }

    $html = '<p>Monitor reads only the tail of a selected log. Log contents are escaped before display.</p>';

    if (empty($files)) {
        return $html . '<p>No log files found.</p>';
    }

    $html .= '<form method="get" action="'
          . MONITOR_ADMIN_h($_CONF['site_admin_url'] . '/plugins/monitor/index.php')
          . '">'
          . '<input type="hidden" name="view" value="logs">'
          . '<label>' . MONITOR_ADMIN_h($LANG_MONITOR_1['file']) . ' '
          . '<select name="log">';

    foreach ($files as $file) {
        $html .= '<option value="' . MONITOR_ADMIN_h($file) . '"'
              . ($file === $selected ? ' selected' : '')
              . '>' . MONITOR_ADMIN_h($file) . '</option>';
    }

    $html .= '</select></label> '
          . '<button type="submit">'
          . MONITOR_ADMIN_h($LANG_MONITOR_1['view_logs'])
          . '</button></form>';

    if ($selected !== '') {
        $path = $_CONF['path_log'] . $selected;
        $size = @filesize($path);
        $contents = MONITOR_readTail($path, 131072);

        $html .= '<h3>' . MONITOR_ADMIN_h($selected) . '</h3>';
        if ($size !== false) {
            $html .= '<p>Size: ' . MONITOR_ADMIN_h(MONITOR_HEALTH_formatBytes($size)) . '</p>';
        }

        $html .= '<pre style="max-height:650px;overflow:auto;white-space:pre-wrap">'
              . MONITOR_ADMIN_h($contents)
              . '</pre>';

        $token = SEC_createToken();
        $html .= '<form method="post" action="'
              . MONITOR_ADMIN_h($_CONF['site_admin_url'] . '/plugins/monitor/index.php')
              . '">'
              . '<input type="hidden" name="view" value="logs">'
              . '<input type="hidden" name="action" value="clear_log">'
              . '<input type="hidden" name="log" value="' . MONITOR_ADMIN_h($selected) . '">'
              . '<input type="hidden" name="' . MONITOR_ADMIN_h(CSRF_TOKEN)
              . '" value="' . MONITOR_ADMIN_h($token) . '">'
              . '<button type="submit">'
              . MONITOR_ADMIN_h($LANG_MONITOR_1['clear_logs'])
              . '</button></form>';
    }

    return $html;
}

/**
 * Render security observations and Ban integration state.
 *
 * @return string
 */
function MONITOR_ADMIN_security()
{
    global $_TABLES, $LANG_MONITOR_1;

    $html = '<p>' . MONITOR_ADMIN_h($LANG_MONITOR_1['legacy_ban_notice']) . '</p>';

    $capabilities = MONITOR_BAN_capabilities();
    $version = MONITOR_BAN_version();

    $html .= '<h3>' . MONITOR_ADMIN_h($LANG_MONITOR_1['ban_integration']) . '</h3>';
    $html .= '<table class="admin-list">'
          . '<tr><th>Installed/enabled</th><td>'
          . (!empty($capabilities['installed']) ? 'Yes' : 'No')
          . '</td></tr>'
          . '<tr><th>Version</th><td>'
          . MONITOR_ADMIN_h($version === '' ? 'unknown' : $version)
          . '</td></tr>'
          . '<tr><th>IP ban request capability</th><td>'
          . (!empty($capabilities['request_ip_ban']) ? 'Available' : 'Unavailable')
          . '</td></tr>'
          . '<tr><th>Direct Ban SQL coupling</th><td>No</td></tr>'
          . '</table>';

    $html .= '<h3>' . MONITOR_ADMIN_h($LANG_MONITOR_1['security_observations']) . '</h3>';

    if (!DB_checkTableExists('monitor_ban')) {
        return $html . '<p>No legacy Monitor security table is present.</p>';
    }

    $result = DB_query(
        "SELECT bantype, COUNT(*) AS total, MAX(created) AS last_seen "
        . "FROM {$_TABLES['monitor_ban']} GROUP BY bantype ORDER BY bantype",
        1
    );

    $html .= '<table class="admin-list" style="width:100%">'
          . '<thead><tr><th>Type</th><th>Count</th><th>Last seen</th></tr></thead><tbody>';

    $rows = 0;
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

    if ($rows === 0) {
        $html .= '<tr><td colspan="3">No recent security observations.</td></tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}

/**
 * Render local plugin state without installing or downloading executable code.
 *
 * @return string
 */
function MONITOR_ADMIN_plugins()
{
    global $_TABLES;

    $html = '<p>Monitor 1.4.0 treats plugin updates as advice first. This view performs no installation and downloads no executable code.</p>';

    $result = DB_query(
        "SELECT pi_name, pi_version, pi_enabled, pi_gl_version, pi_homepage "
        . "FROM {$_TABLES['plugins']} ORDER BY pi_name"
    );

    $html .= '<table class="admin-list" style="width:100%">'
          . '<thead><tr>'
          . '<th>Plugin</th><th>Installed</th><th>Code</th><th>Enabled</th><th>Geeklog requirement</th>'
          . '</tr></thead><tbody>';

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

        $html .= '<tr><td>' . MONITOR_ADMIN_h($name) . '</td>'
              . '<td>' . MONITOR_ADMIN_h($installed) . '</td>'
              . '<td>' . MONITOR_ADMIN_h($code === '' ? 'unknown' : $code) . '</td>'
              . '<td>' . (!empty($row['pi_enabled']) ? 'Yes' : 'No') . '</td>'
              . '<td>' . MONITOR_ADMIN_h(isset($row['pi_gl_version']) ? $row['pi_gl_version'] : '') . '</td></tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}

// ---------------------------------------------------------------------------
// State-changing actions
// ---------------------------------------------------------------------------

$requestedView = isset($_REQUEST['view']) ? COM_applyFilter($_REQUEST['view']) : 'overview';
$allowedViews = array('overview', 'logs', 'security', 'plugins');
if (!in_array($requestedView, $allowedViews, true)) {
    $requestedView = 'overview';
}

$action = isset($_POST['action']) ? COM_applyFilter($_POST['action']) : '';
$statusMessage = '';

if ($action === 'clear_log') {
    if (!SEC_checkToken()) {
        COM_accessLog('Monitor rejected a log clear request because the security token was invalid.');
        $statusMessage = '<p><strong>Security token validation failed. The log was not changed.</strong></p>';
    } else {
        $files = MONITOR_ADMIN_logFiles();
        $requestedLog = isset($_POST['log'])
            ? MONITOR_ADMIN_validLog($_POST['log'], $files)
            : '';

        if ($requestedLog === '') {
            $statusMessage = '<p><strong>Invalid log file. Nothing was changed.</strong></p>';
        } else {
            $path = $_CONF['path_log'] . $requestedLog;
            $handle = @fopen($path, 'wb');
            if ($handle === false) {
                $statusMessage = '<p><strong>The log file could not be cleared.</strong></p>';
            } else {
                fwrite($handle, MONITOR_timestamp() . " - Log File Cleared by Monitor administrator\n");
                fclose($handle);
                COM_errorLog('MONITOR - Administrator cleared log file: ' . $requestedLog);
                $statusMessage = '<p><strong>Log file cleared.</strong></p>';
            }
        }
    }

    $requestedView = 'logs';
}

// ---------------------------------------------------------------------------
// Render
// ---------------------------------------------------------------------------

$content = MONITOR_ADMIN_navigation($requestedView);
$content .= $statusMessage;

switch ($requestedView) {
    case 'logs':
        $title = $LANG_MONITOR_1['logs'];
        $content .= MONITOR_ADMIN_logs();
        break;

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