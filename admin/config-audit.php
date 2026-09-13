<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/config-audit.php                                                    |
// |                                                                           |
// | Focused read-only audit of siteconfig.php versus Core conf_values.        |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorConfigAudit.php';

if (!SEC_inGroup('Root')) {
    $display = COM_showMessageText('Access reserved for Root administrators.', 'Access denied');
    COM_accessLog('Non-Root user tried to access the Monitor configuration audit.');
    COM_output(COM_createHTMLDocument($display, array('pagetitle' => 'Access denied')));
    exit;
}

header('X-Robots-Tag: noindex, nofollow, noarchive', true);

function MONITOR_CONFIG_ADMIN_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function MONITOR_CONFIG_ADMIN_value($row, $field)
{
    if (!empty($row['sensitive'])) {
        return '[REDACTED]';
    }

    return MONITOR_CONFIG_ADMIN_h(
        MONITOR_CONFIG_AUDIT_displayValue(isset($row[$field]) ? $row[$field] : null)
    );
}

function MONITOR_CONFIG_ADMIN_card($row)
{
    $status = isset($row['status']) ? $row['status'] : '';
    $border = '#d7dde2';
    $background = '#fff';

    if ($status === 'DIFFERENT' || $status === 'DB DECODE ERROR') {
        $border = '#ef9a9a';
        $background = '#fff7f7';
    } elseif ($status === 'FILE ONLY' || $status === 'DB = unset') {
        $border = '#ffe082';
        $background = '#fffaf0';
    }

    if (!empty($row['path']['checked']) && empty($row['path']['exists'])) {
        $border = '#ef9a9a';
        $background = '#fff7f7';
    }

    $html = '<section style="border:1px solid ' . $border . ';background:' . $background
          . ';border-radius:8px;padding:14px;margin:0 0 12px 0">';

    $html .= '<div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:space-between;align-items:center">'
          . '<strong><code>' . MONITOR_CONFIG_ADMIN_h($row['key']) . '</code></strong>'
          . '<span style="font-weight:bold">' . MONITOR_CONFIG_ADMIN_h($status) . '</span>'
          . '</div>';

    $html .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;margin-top:12px">';

    $html .= '<div><div style="font-size:.9em;color:#666">siteconfig.php</div><pre style="white-space:pre-wrap;word-break:break-word;margin:4px 0 0">'
          . MONITOR_CONFIG_ADMIN_value($row, 'site_value') . '</pre></div>';

    $html .= '<div><div style="font-size:.9em;color:#666">Database</div><pre style="white-space:pre-wrap;word-break:break-word;margin:4px 0 0">'
          . ($row['db_exists'] ? MONITOR_CONFIG_ADMIN_value($row, 'db_value') : 'ABSENT')
          . '</pre></div>';

    $html .= '</div>';

    $html .= '<div style="margin-top:10px;font-size:.95em"><strong>Priority:</strong> '
          . MONITOR_CONFIG_ADMIN_h($row['priority']) . '</div>';

    if (!empty($row['path']['checked'])) {
        $html .= '<div style="margin-top:6px"><strong>Path:</strong> '
              . (!empty($row['path']['exists']) ? 'exists' : '<strong>missing</strong>')
              . '</div>';
    }

    if (!empty($row['sql'])) {
        $html .= '<details style="margin-top:10px"><summary>Candidate SQL correction</summary>'
              . '<pre style="white-space:pre-wrap;word-break:break-word;overflow:auto;margin-top:8px">'
              . MONITOR_CONFIG_ADMIN_h($row['sql'])
              . '</pre></details>';
    }

    $html .= '</section>';

    return $html;
}

$audit = MONITOR_CONFIG_AUDIT_collect();
$summary = $audit['summary'];
$attentionRows = array();
$normalRows = array();

foreach ($audit['rows'] as $row) {
    if (!empty($row['attention'])) {
        $attentionRows[] = $row;
    } else {
        $normalRows[] = $row;
    }
}

$content = '<p><a href="index.php?view=overview">&larr; Monitor overview</a></p>';
$content .= '<div style="padding:14px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:18px">'
          . '<strong>Read-only configuration audit.</strong><br>'
          . 'This view checks values explicitly defined in <code>siteconfig.php</code> against the same Core keys stored in <code>conf_values</code>. '
          . 'It does not list unrelated database-only configuration and never changes Geeklog automatically.'
          . '</div>';

$content .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:18px">';
$content .= '<div style="padding:12px;border:1px solid #ef9a9a;border-radius:7px;background:#fff7f7"><strong style="font-size:1.35em">'
          . (int) $summary['different'] . '</strong><br>Different</div>';
$content .= '<div style="padding:12px;border:1px solid #ffe082;border-radius:7px;background:#fffaf0"><strong style="font-size:1.35em">'
          . (int) $summary['file_only'] . '</strong><br>File only</div>';
$content .= '<div style="padding:12px;border:1px solid #ffe082;border-radius:7px;background:#fffaf0"><strong style="font-size:1.35em">'
          . (int) $summary['db_unset'] . '</strong><br>DB unset</div>';
$content .= '<div style="padding:12px;border:1px solid #ef9a9a;border-radius:7px;background:#fff7f7"><strong style="font-size:1.35em">'
          . (int) $summary['invalid_paths'] . '</strong><br>Invalid paths</div>';
$content .= '</div>';

$content .= '<div style="margin-bottom:18px;font-size:.95em;color:#555">'
          . '<strong>Active host:</strong> '
          . MONITOR_CONFIG_ADMIN_h(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')
          . '<br><strong>siteconfig.php:</strong> <code style="word-break:break-all">'
          . MONITOR_CONFIG_ADMIN_h($audit['siteconfig_path'])
          . '</code></div>';

if (!$audit['siteconfig_readable']) {
    $content .= '<div style="padding:12px;border:1px solid #ef9a9a;background:#fff7f7;border-radius:7px;margin-bottom:18px">'
             . '<strong>Warning:</strong> Monitor could not read the active siteconfig.php, so the comparison is incomplete.'
             . '</div>';
}

$content .= '<h3>Items to review</h3>';
if (empty($attentionRows)) {
    $content .= '<div style="padding:14px;border:1px solid #a5d6a7;background:#f4fbf5;border-radius:8px;margin-bottom:18px">'
             . '<strong>No configuration difference requiring attention was detected.</strong>'
             . '</div>';
} else {
    foreach ($attentionRows as $row) {
        $content .= MONITOR_CONFIG_ADMIN_card($row);
    }
}

if (!empty($normalRows)) {
    $content .= '<details style="margin-top:22px">'
             . '<summary style="cursor:pointer;font-weight:bold">Secondary details: '
             . (int) count($normalRows)
             . ' identical or expected file-only Core value(s)</summary>'
             . '<div style="margin-top:12px">';

    foreach ($normalRows as $row) {
        $content .= MONITOR_CONFIG_ADMIN_card($row);
    }

    $content .= '</div></details>';
}

$content .= '<p style="margin-top:22px;color:#666;font-size:.92em">'
          . 'Sensitive values are redacted. Candidate SQL is shown only for non-sensitive differences and is never executed automatically.'
          . '</p>';

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
