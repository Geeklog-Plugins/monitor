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
    $display = COM_showMessageText(
        $LANG_MONITOR_1['config_audit_root_only'],
        $LANG_MONITOR_1['config_audit_access_denied']
    );
    COM_accessLog('Non-Root user tried to access the Monitor configuration audit.');
    COM_output(COM_createHTMLDocument(
        $display,
        array('pagetitle' => $LANG_MONITOR_1['config_audit_access_denied'])
    ));
    exit;
}

header('X-Robots-Tag: noindex, nofollow, noarchive', true);

function MONITOR_CONFIG_ADMIN_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function MONITOR_CONFIG_ADMIN_value($row, $field)
{
    global $LANG_MONITOR_1;

    if (!empty($row['sensitive'])) {
        return MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_redacted']);
    }

    $value = isset($row[$field]) ? $row[$field] : null;

    if ($value === true) {
        return MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_value_true']);
    }
    if ($value === false) {
        return MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_value_false']);
    }
    if ($value === null) {
        return MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_value_null']);
    }
    if (is_array($value)) {
        return MONITOR_CONFIG_ADMIN_h(print_r($value, true));
    }
    if (is_object($value)) {
        return MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_value_object']);
    }

    return MONITOR_CONFIG_ADMIN_h((string) $value);
}

function MONITOR_CONFIG_ADMIN_statusText($status)
{
    global $LANG_MONITOR_1;

    $map = array(
        'identical' => 'config_audit_status_identical',
        'core_file' => 'config_audit_status_core_file',
        'file_only' => 'config_audit_status_file_only',
        'db_unset' => 'config_audit_status_db_unset',
        'different' => 'config_audit_status_different',
        'decode_error' => 'config_audit_status_decode_error',
        'invalid_path' => 'config_audit_status_invalid_path'
    );

    if (!isset($map[$status]) || !isset($LANG_MONITOR_1[$map[$status]])) {
        return $status;
    }

    return $LANG_MONITOR_1[$map[$status]];
}

function MONITOR_CONFIG_ADMIN_levelText($level)
{
    global $LANG_MONITOR_1;

    $map = array(
        'ok' => 'config_audit_level_ok',
        'info' => 'config_audit_level_info',
        'review' => 'config_audit_level_review',
        'warning' => 'config_audit_level_warning'
    );

    if (!isset($map[$level]) || !isset($LANG_MONITOR_1[$map[$level]])) {
        return $level;
    }

    return $LANG_MONITOR_1[$map[$level]];
}

function MONITOR_CONFIG_ADMIN_card($row)
{
    global $LANG_MONITOR_1;

    $level = isset($row['level']) ? $row['level'] : 'info';
    $border = '#d7dde2';
    $background = '#fff';

    if ($level === 'warning') {
        $border = '#ef9a9a';
        $background = '#fff7f7';
    } elseif ($level === 'review') {
        $border = '#ffe082';
        $background = '#fffaf0';
    } elseif ($level === 'ok') {
        $border = '#a5d6a7';
        $background = '#f4fbf5';
    } elseif ($level === 'info') {
        $border = '#90caf9';
        $background = '#f5faff';
    }

    $statusText = MONITOR_CONFIG_ADMIN_statusText($row['status']);
    $levelText = MONITOR_CONFIG_ADMIN_levelText($level);
    $whyText = isset($LANG_MONITOR_1[$row['why_key']]) ? $LANG_MONITOR_1[$row['why_key']] : '';
    $actionText = isset($LANG_MONITOR_1[$row['action_key']]) ? $LANG_MONITOR_1[$row['action_key']] : '';

    $html = '<section style="border:1px solid ' . $border . ';background:' . $background
          . ';border-radius:8px;padding:14px;margin:0 0 12px 0">';

    $html .= '<div style="display:flex;flex-wrap:wrap;gap:8px;justify-content:space-between;align-items:center">'
          . '<strong><code>' . MONITOR_CONFIG_ADMIN_h($row['key']) . '</code></strong>'
          . '<span style="font-weight:bold">'
          . MONITOR_CONFIG_ADMIN_h($levelText . ' — ' . $statusText)
          . '</span></div>';

    $html .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:10px;margin-top:12px">';

    $html .= '<div><div style="font-size:.9em;color:#666">'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_source_siteconfig'])
          . '</div><pre style="white-space:pre-wrap;word-break:break-word;margin:4px 0 0">'
          . MONITOR_CONFIG_ADMIN_value($row, 'site_value') . '</pre></div>';

    $html .= '<div><div style="font-size:.9em;color:#666">'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_source_database'])
          . '</div><pre style="white-space:pre-wrap;word-break:break-word;margin:4px 0 0">'
          . (!empty($row['db_exists'])
              ? MONITOR_CONFIG_ADMIN_value($row, 'db_value')
              : MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_absent']))
          . '</pre></div>';

    $html .= '<div><div style="font-size:.9em;color:#666">'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_effective_value'])
          . '</div><pre style="white-space:pre-wrap;word-break:break-word;margin:4px 0 0">'
          . MONITOR_CONFIG_ADMIN_value($row, 'effective_value') . '</pre></div>';

    $html .= '</div>';

    $html .= '<div style="margin-top:10px;font-size:.95em"><strong>'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_priority'])
          . '</strong> '
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_source_siteconfig'])
          . '</div>';

    if ($whyText !== '') {
        $html .= '<div style="margin-top:8px"><strong>'
              . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_why'])
              . '</strong> ' . MONITOR_CONFIG_ADMIN_h($whyText) . '</div>';
    }

    if ($actionText !== '') {
        $html .= '<div style="margin-top:8px"><strong>'
              . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_recommendation'])
              . '</strong> ' . MONITOR_CONFIG_ADMIN_h($actionText) . '</div>';
    }

    if (!empty($row['path']['checked'])) {
        $pathText = !empty($row['path']['exists'])
            ? $LANG_MONITOR_1['config_audit_path_exists']
            : $LANG_MONITOR_1['config_audit_path_missing'];

        $html .= '<div style="margin-top:8px"><strong>'
              . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_path'])
              . '</strong> ' . MONITOR_CONFIG_ADMIN_h($pathText) . '</div>';
    }

    if (!empty($row['sql'])) {
        $html .= '<details style="margin-top:10px"><summary>'
              . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_optional_sql'])
              . '</summary><pre style="white-space:pre-wrap;word-break:break-word;overflow:auto;margin-top:8px">'
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
    if ($row['level'] === 'warning' || $row['level'] === 'review' || $row['level'] === 'info') {
        $attentionRows[] = $row;
    } else {
        $normalRows[] = $row;
    }
}

$content = '<p><a href="index.php?view=overview">&larr; '
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_back'])
          . '</a></p>';

$content .= '<div style="padding:14px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:18px">'
          . '<strong>' . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_intro_title']) . '</strong><br>'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_intro'])
          . '</div>';

$content .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin-bottom:18px">';
$content .= '<div style="padding:12px;border:1px solid #ef9a9a;border-radius:7px;background:#fff7f7"><strong style="font-size:1.35em">'
          . (int) $summary['issues'] . '</strong><br>'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_issues']) . '</div>';
$content .= '<div style="padding:12px;border:1px solid #ffe082;border-radius:7px;background:#fffaf0"><strong style="font-size:1.35em">'
          . (int) $summary['review'] . '</strong><br>'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_review']) . '</div>';
$content .= '<div style="padding:12px;border:1px solid #a5d6a7;border-radius:7px;background:#f4fbf5"><strong style="font-size:1.35em">'
          . (int) $summary['expected'] . '</strong><br>'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_expected']) . '</div>';
$content .= '<div style="padding:12px;border:1px solid #ef9a9a;border-radius:7px;background:#fff7f7"><strong style="font-size:1.35em">'
          . (int) $summary['invalid_paths'] . '</strong><br>'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_invalid_paths']) . '</div>';
$content .= '</div>';

$content .= '<div style="margin-bottom:18px;font-size:.95em;color:#555">'
          . '<strong>' . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_active_host']) . '</strong> '
          . MONITOR_CONFIG_ADMIN_h(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '')
          . '<br><strong>' . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_siteconfig']) . '</strong> '
          . '<code style="word-break:break-all">'
          . MONITOR_CONFIG_ADMIN_h($audit['siteconfig_path'])
          . '</code></div>';

if (!$audit['siteconfig_readable']) {
    $content .= '<div style="padding:12px;border:1px solid #ef9a9a;background:#fff7f7;border-radius:7px;margin-bottom:18px">'
             . '<strong>' . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_unreadable_title']) . '</strong> '
             . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_unreadable'])
             . '</div>';
}

$content .= '<h3>' . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_items_review']) . '</h3>';
if (empty($attentionRows)) {
    $content .= '<div style="padding:14px;border:1px solid #a5d6a7;background:#f4fbf5;border-radius:8px;margin-bottom:18px">'
             . '<strong>' . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_no_issues']) . '</strong>'
             . '</div>';
} else {
    foreach ($attentionRows as $row) {
        $content .= MONITOR_CONFIG_ADMIN_card($row);
    }
}

if (!empty($normalRows)) {
    $secondaryLabel = sprintf(
        $LANG_MONITOR_1['config_audit_secondary'],
        (int) count($normalRows)
    );

    $content .= '<details style="margin-top:22px">'
             . '<summary style="cursor:pointer;font-weight:bold">'
             . MONITOR_CONFIG_ADMIN_h($secondaryLabel)
             . '</summary><div style="margin-top:12px">';

    foreach ($normalRows as $row) {
        $content .= MONITOR_CONFIG_ADMIN_card($row);
    }

    $content .= '</div></details>';
}

$content .= '<p style="margin-top:22px;color:#666;font-size:.92em">'
          . MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_footer'])
          . '</p>';

$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file(array('admin' => 'administration.thtml'));
$T->set_var(array(
    'title' => MONITOR_CONFIG_ADMIN_h($LANG_MONITOR_1['config_audit_title']),
    'status_msg' => '',
    'admin_body' => $content
));
$T->parse('output', 'admin');

$body = $T->finish($T->get_var('output'));
$display = COM_createHTMLDocument(
    $body,
    array('pagetitle' => $LANG_MONITOR_1['config_audit_page_title'])
);
COM_output($display);
