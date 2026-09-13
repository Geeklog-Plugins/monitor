<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/config-audit.php                                                    |
// |                                                                           |
// | Read-only audit of siteconfig.php versus Geeklog Core conf_values.        |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorConfigAudit.php';

if (!SEC_hasRights('monitor.admin')) {
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    COM_output(COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30])));
    exit;
}

header('X-Robots-Tag: noindex, nofollow, noarchive', true);

function MONITOR_CONFIG_ADMIN_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function MONITOR_CONFIG_ADMIN_value($value)
{
    return MONITOR_CONFIG_ADMIN_h(MONITOR_CONFIG_AUDIT_displayValue($value));
}

$audit = MONITOR_CONFIG_AUDIT_collect();
$summary = $audit['summary'];

$content = '<p><a href="index.php">&larr; Monitor overview</a></p>';
$content .= '<p><strong>Read-only mode.</strong> This audit does not modify Geeklog, siteconfig.php or the database.</p>';
$content .= '<table class="admin-list" style="width:100%;max-width:900px">'
          . '<tr><th>Active host</th><td>'
          . MONITOR_CONFIG_ADMIN_h(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')
          . '</td></tr>'
          . '<tr><th>siteconfig.php</th><td><code>'
          . MONITOR_CONFIG_ADMIN_h($audit['siteconfig_path'])
          . '</code></td></tr>'
          . '<tr><th>Configuration table</th><td><code>'
          . MONITOR_CONFIG_ADMIN_h($_TABLES['conf_values'])
          . '</code></td></tr>'
          . '</table>';

if (!$audit['siteconfig_readable']) {
    $content .= '<p><strong>Warning:</strong> Monitor could not read the active siteconfig.php. '
             . 'Database-only configuration is still shown, but file comparison is incomplete.</p>';
}

$content .= '<h3>Summary</h3>';
$content .= '<table class="admin-list"><tr>'
          . '<th>Identical</th><th>Different</th><th>Core file</th>'
          . '<th>File only</th><th>Database only</th><th>DB unset</th>'
          . '<th>Invalid paths</th><th>Decode errors</th></tr><tr>'
          . '<td>' . (int) $summary['identical'] . '</td>'
          . '<td>' . (int) $summary['different'] . '</td>'
          . '<td>' . (int) $summary['core_file'] . '</td>'
          . '<td>' . (int) $summary['file_only'] . '</td>'
          . '<td>' . (int) $summary['database_only'] . '</td>'
          . '<td>' . (int) $summary['db_unset'] . '</td>'
          . '<td>' . (int) $summary['invalid_paths'] . '</td>'
          . '<td>' . (int) $summary['decode_errors'] . '</td>'
          . '</tr></table>';

$content .= '<h3>Comparison</h3>';
$content .= '<div style="overflow:auto"><table class="admin-list" style="width:100%">'
          . '<thead><tr>'
          . '<th>Parameter</th><th>siteconfig.php</th><th>Database</th>'
          . '<th>Priority</th><th>Effective value</th><th>Path</th><th>Status</th><th>SQL</th>'
          . '</tr></thead><tbody>';

$sqlSuggestions = array();

foreach ($audit['rows'] as $row) {
    $pathLabel = '&mdash;';
    if ($row['path']['checked']) {
        $pathLabel = $row['path']['exists'] ? 'OK' : '<strong>Missing</strong>';
    }

    if ($row['sql'] !== '') {
        $sqlSuggestions[$row['key']] = $row['sql'];
    }

    $content .= '<tr>'
              . '<td><code>' . MONITOR_CONFIG_ADMIN_h($row['key']) . '</code></td>'
              . '<td><pre style="white-space:pre-wrap;margin:0">'
              . ($row['site_exists'] ? MONITOR_CONFIG_ADMIN_value($row['site_value']) : '&mdash;')
              . '</pre></td>'
              . '<td><pre style="white-space:pre-wrap;margin:0">'
              . ($row['db_exists'] ? MONITOR_CONFIG_ADMIN_value($row['db_value']) : 'ABSENT')
              . '</pre></td>'
              . '<td>' . MONITOR_CONFIG_ADMIN_h($row['priority']) . '</td>'
              . '<td><pre style="white-space:pre-wrap;margin:0">'
              . MONITOR_CONFIG_ADMIN_value($row['effective_value'])
              . '</pre></td>'
              . '<td>' . $pathLabel . '</td>'
              . '<td><strong>' . MONITOR_CONFIG_ADMIN_h($row['status']) . '</strong></td>'
              . '<td>' . ($row['sql'] !== '' ? 'suggested' : '&mdash;') . '</td>'
              . '</tr>';
}

$content .= '</tbody></table></div>';

$content .= '<h3>Candidate SQL corrections</h3>';
if (empty($sqlSuggestions)) {
    $content .= '<p>No candidate SQL correction.</p>';
} else {
    $content .= '<p><strong>These statements are never executed automatically.</strong> '
             . 'They are shown only when siteconfig.php and Core conf_values contain different values. '
             . 'For physical paths, the siteconfig.php path must exist before a statement is suggested.</p>';

    foreach ($sqlSuggestions as $key => $sql) {
        $content .= '<h4>' . MONITOR_CONFIG_ADMIN_h($key) . '</h4>'
                  . '<pre style="white-space:pre-wrap;overflow:auto">'
                  . MONITOR_CONFIG_ADMIN_h($sql)
                  . '</pre>';
    }
}

$content .= '<h3>Effective Core configuration</h3>';
$content .= '<pre style="white-space:pre-wrap;overflow:auto">';
foreach ($audit['rows'] as $row) {
    $content .= MONITOR_CONFIG_ADMIN_h(
        "\$_CONF['" . $row['key'] . "'] = "
        . var_export($row['effective_value'], true) . ";\n"
    );
}
$content .= '</pre>';

$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file(array('admin' => 'administration.thtml'));
$T->set_var(array(
    'title' => 'Configuration audit',
    'status_msg' => '',
    'admin_body' => $content
));
$T->parse('output', 'admin');

$body = $T->finish($T->get_var('output'));
$display = COM_createHTMLDocument($body, array('pagetitle' => 'Monitor configuration audit'));
COM_output($display);
