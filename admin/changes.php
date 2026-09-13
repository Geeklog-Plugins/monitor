<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/changes.php                                                         |
// |                                                                           |
// | Compare lightweight site snapshots and recent error.log activity.         |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorChanges.php';

if (!SEC_hasRights('monitor.admin')) {
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    COM_output(COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30])));
    exit;
}

header('X-Robots-Tag: noindex, nofollow, noarchive', true);

function MONITOR_CHANGES_ADMIN_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function MONITOR_CHANGES_ADMIN_bytes($bytes)
{
    if ($bytes === null || !is_numeric($bytes)) {
        global $LANG_MONITOR_1;
        return $LANG_MONITOR_1['changes_unknown'];
    }

    $bytes = (float) $bytes;
    $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');
    $unit = 0;
    while ($bytes >= 1024 && $unit < count($units) - 1) {
        $bytes /= 1024;
        $unit++;
    }

    return number_format($bytes, $unit === 0 ? 0 : 1) . ' ' . $units[$unit];
}

function MONITOR_CHANGES_ADMIN_date($timestamp)
{
    if (empty($timestamp)) {
        return '';
    }

    return date('Y-m-d H:i:s', (int) $timestamp);
}

function MONITOR_CHANGES_ADMIN_navigation()
{
    global $_CONF, $LANG_MONITOR_1;

    $base = $_CONF['site_admin_url'] . '/plugins/monitor/';
    $items = array(
        array($LANG_MONITOR_1['home'], $base . 'index.php?view=overview'),
        array($LANG_MONITOR_1['changes'], $base . 'changes.php'),
        array($LANG_MONITOR_1['security'], $base . 'index.php?view=security'),
        array($LANG_MONITOR_1['updates'], $base . 'index.php?view=plugins')
    );

    $html = '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0 0 18px 0">';
    foreach ($items as $item) {
        $active = ($item[0] === $LANG_MONITOR_1['changes']);
        $style = 'display:inline-block;padding:7px 11px;border:1px solid #c7ccd1;border-radius:5px;text-decoration:none;';
        if ($active) {
            $style .= 'font-weight:bold;background:#eef2f5;';
        }
        $html .= '<a style="' . $style . '" href="' . MONITOR_CHANGES_ADMIN_h($item[1]) . '">'
              . MONITOR_CHANGES_ADMIN_h($item[0]) . '</a>';
    }
    $html .= '</div>';

    return $html;
}

function MONITOR_CHANGES_ADMIN_summaryCard($label, $value, $kind)
{
    $border = '#cfd8dc';
    $background = '#fff';
    if ($kind === 'warning') {
        $border = '#ffe082';
        $background = '#fffaf0';
    } elseif ($kind === 'ok') {
        $border = '#a5d6a7';
        $background = '#f4fbf5';
    } elseif ($kind === 'info') {
        $border = '#90caf9';
        $background = '#f5faff';
    }

    return '<div style="min-width:110px;flex:1;padding:12px;border:1px solid ' . $border
         . ';background:' . $background . ';border-radius:7px">'
         . '<div style="font-size:1.4em;font-weight:bold">' . (int) $value . '</div>'
         . '<div style="margin-top:4px">' . MONITOR_CHANGES_ADMIN_h($label) . '</div>'
         . '</div>';
}

function MONITOR_CHANGES_ADMIN_changeLabel($code)
{
    global $LANG_MONITOR_1;

    $key = 'changes_code_' . $code;

    return isset($LANG_MONITOR_1[$key]) ? $LANG_MONITOR_1[$key] : $code;
}

function MONITOR_CHANGES_ADMIN_value($value, $change)
{
    global $LANG_MONITOR_1;

    if ($change['code'] === 'plugin_enabled' || $change['code'] === 'plugin_disabled') {
        return !empty($value)
            ? $LANG_MONITOR_1['changes_enabled']
            : $LANG_MONITOR_1['changes_disabled'];
    }
    if ($change['code'] === 'disk_free_decreased') {
        return MONITOR_CHANGES_ADMIN_bytes($value);
    }
    if ($value === '' || $value === null) {
        return '—';
    }

    return (string) $value;
}

$notice = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['capture'])) {
    $tokenValid = function_exists('SEC_checkToken') ? SEC_checkToken() : false;
    if ($tokenValid && MONITOR_CHANGES_capture('manual')) {
        $notice = '<div style="padding:10px;border:1px solid #a5d6a7;background:#f4fbf5;border-radius:7px;margin-bottom:14px">'
                . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_capture_ok']) . '</div>';
    } elseif ($tokenValid) {
        $notice = '<div style="padding:10px;border:1px solid #ef9a9a;background:#fff7f7;border-radius:7px;margin-bottom:14px">'
                . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_capture_failed']) . '</div>';
    }
}

$history = MONITOR_CHANGES_readHistory();
if (empty($history)) {
    if (MONITOR_CHANGES_capture('baseline')) {
        $notice = '<div style="padding:10px;border:1px solid #90caf9;background:#f5faff;border-radius:7px;margin-bottom:14px">'
                . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_baseline_created']) . '</div>';
    } else {
        $notice = '<div style="padding:10px;border:1px solid #ef9a9a;background:#fff7f7;border-radius:7px;margin-bottom:14px">'
                . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_capture_failed']) . '</div>';
    }
}

$report = MONITOR_CHANGES_report();
$current = isset($report['current']) && is_array($report['current'])
    ? $report['current']
    : MONITOR_CHANGES_collectSnapshot('preview');
$changes = isset($report['changes']) && is_array($report['changes']) ? $report['changes'] : array();
$errorDelta = isset($report['error_delta']) && is_array($report['error_delta']) ? $report['error_delta'] : array();

$pluginChanges = 0;
foreach ($changes as $change) {
    if (isset($change['type']) && $change['type'] === 'plugin') {
        $pluginChanges++;
    }
}
$logPatterns = isset($errorDelta['signatures']) && is_array($errorDelta['signatures'])
    ? count($errorDelta['signatures'])
    : 0;

$content = MONITOR_CHANGES_ADMIN_navigation();
$content .= '<div style="padding:13px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:14px">'
          . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_intro']) . '</div>';
$content .= $notice;

$content .= '<form method="post" action="changes.php" style="margin:0 0 16px 0">'
          . '<input type="hidden" name="capture" value="1">';
if (function_exists('SEC_createToken')) {
    $content .= '<input type="hidden" name="' . CSRF_TOKEN . '" value="'
              . MONITOR_CHANGES_ADMIN_h(SEC_createToken()) . '">';
}
$content .= '<button type="submit" style="padding:8px 12px">'
          . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_capture']) . '</button></form>';

$content .= '<div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:18px">'
          . MONITOR_CHANGES_ADMIN_summaryCard($LANG_MONITOR_1['changes_summary_changes'], count($changes), empty($changes) ? 'ok' : 'info')
          . MONITOR_CHANGES_ADMIN_summaryCard($LANG_MONITOR_1['changes_summary_plugins'], $pluginChanges, 'info')
          . MONITOR_CHANGES_ADMIN_summaryCard($LANG_MONITOR_1['changes_summary_log'], $logPatterns, $logPatterns > 0 ? 'warning' : 'ok')
          . MONITOR_CHANGES_ADMIN_summaryCard($LANG_MONITOR_1['changes_summary_snapshots'], isset($report['history_count']) ? $report['history_count'] : 0, 'info')
          . '</div>';

if (empty($report['ready'])) {
    $content .= '<div style="padding:12px;border:1px solid #90caf9;background:#f5faff;border-radius:8px;margin-bottom:18px">'
              . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_waiting']) . '</div>';
} else {
    $content .= '<div style="margin-bottom:16px;color:#555;font-size:.93em"><strong>'
              . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_period']) . '</strong> '
              . MONITOR_CHANGES_ADMIN_h(MONITOR_CHANGES_ADMIN_date($report['previous']['timestamp']))
              . ' → '
              . MONITOR_CHANGES_ADMIN_h(MONITOR_CHANGES_ADMIN_date($report['current']['timestamp']))
              . '</div>';

    if (empty($changes)) {
        $content .= '<div style="padding:12px;border:1px solid #a5d6a7;background:#f4fbf5;border-radius:8px;margin-bottom:18px">'
                  . '<strong>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_none']) . '</strong></div>';
    } else {
        $content .= '<h3>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_detected']) . '</h3>';
        $content .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:10px;margin-bottom:22px">';
        foreach ($changes as $change) {
            $warning = isset($change['severity']) && $change['severity'] === 'warning';
            $border = $warning ? '#ffe082' : '#d7dde2';
            $background = $warning ? '#fffaf0' : '#fff';
            $content .= '<section style="border:1px solid ' . $border . ';background:' . $background . ';border-radius:8px;padding:12px">'
                      . '<strong>' . MONITOR_CHANGES_ADMIN_h(MONITOR_CHANGES_ADMIN_changeLabel($change['code'])) . '</strong>'
                      . '<div style="margin-top:5px"><code>' . MONITOR_CHANGES_ADMIN_h($change['item']) . '</code></div>'
                      . '<div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:10px;font-size:.92em">'
                      . '<div><span style="color:#666">' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_before']) . '</span><br><strong>'
                      . MONITOR_CHANGES_ADMIN_h(MONITOR_CHANGES_ADMIN_value($change['before'], $change)) . '</strong></div>'
                      . '<div><span style="color:#666">' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_after']) . '</span><br><strong>'
                      . MONITOR_CHANGES_ADMIN_h(MONITOR_CHANGES_ADMIN_value($change['after'], $change)) . '</strong></div>'
                      . '</div></section>';
        }
        $content .= '</div>';
    }

    $content .= '<h3>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_logs']) . '</h3>';
    if (!empty($errorDelta['rotated'])) {
        $content .= '<div style="padding:10px;border:1px solid #ffe082;background:#fffaf0;border-radius:7px;margin-bottom:12px">'
                  . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_log_rotated']) . '</div>';
    } elseif ($logPatterns === 0) {
        $content .= '<p>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_log_none']) . '</p>';
    } else {
        if (!empty($errorDelta['truncated'])) {
            $content .= '<div style="padding:10px;border:1px solid #ffe082;background:#fffaf0;border-radius:7px;margin-bottom:12px">'
                      . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_log_truncated']) . '</div>';
        }
        $content .= '<div style="display:grid;gap:8px;margin-bottom:20px">';
        foreach ($errorDelta['signatures'] as $signature) {
            $content .= '<div style="padding:10px;border:1px solid #d7dde2;border-radius:7px;background:#fff">'
                      . '<strong>' . (int) $signature['count'] . ' '
                      . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_occurrences']) . '</strong>'
                      . '<div style="margin-top:5px"><code style="word-break:break-word">'
                      . MONITOR_CHANGES_ADMIN_h($signature['signature']) . '</code></div></div>';
        }
        $content .= '</div>';
    }
}

$environment = isset($current['environment']) && is_array($current['environment']) ? $current['environment'] : array();
$plugins = isset($current['plugins']) && is_array($current['plugins']) ? $current['plugins'] : array();
$disk = isset($current['disk']) && is_array($current['disk']) ? $current['disk'] : array();
$errorLog = isset($current['error_log']) && is_array($current['error_log']) ? $current['error_log'] : array();

$content .= '<h3>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_current_state']) . '</h3>';
$content .= '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:8px">'
          . '<div style="padding:10px;border:1px solid #d7dde2;border-radius:7px"><small>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_geeklog']) . '</small><br><strong>'
          . MONITOR_CHANGES_ADMIN_h(isset($environment['geeklog']) && $environment['geeklog'] !== '' ? $environment['geeklog'] : $LANG_MONITOR_1['changes_unknown']) . '</strong></div>'
          . '<div style="padding:10px;border:1px solid #d7dde2;border-radius:7px"><small>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_php']) . '</small><br><strong>'
          . MONITOR_CHANGES_ADMIN_h(isset($environment['php']) ? $environment['php'] : $LANG_MONITOR_1['changes_unknown']) . '</strong></div>'
          . '<div style="padding:10px;border:1px solid #d7dde2;border-radius:7px"><small>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_plugins_count']) . '</small><br><strong>'
          . count($plugins) . '</strong></div>'
          . '<div style="padding:10px;border:1px solid #d7dde2;border-radius:7px"><small>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_disk_free']) . '</small><br><strong>'
          . MONITOR_CHANGES_ADMIN_h(MONITOR_CHANGES_ADMIN_bytes(isset($disk['free']) ? $disk['free'] : null)) . '</strong></div>'
          . '<div style="padding:10px;border:1px solid #d7dde2;border-radius:7px"><small>' . MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes_error_log_size']) . '</small><br><strong>'
          . MONITOR_CHANGES_ADMIN_h(MONITOR_CHANGES_ADMIN_bytes(isset($errorLog['size']) ? $errorLog['size'] : null)) . '</strong></div>'
          . '</div>';

$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file(array('admin' => 'administration.thtml'));
$T->set_var(array(
    'title' => MONITOR_CHANGES_ADMIN_h($LANG_MONITOR_1['changes']),
    'status_msg' => '',
    'admin_body' => $content
));
$T->parse('output', 'admin');

$body = $T->finish($T->get_var('output'));
$display = COM_createHTMLDocument($body, array('pagetitle' => $LANG_MONITOR_1['changes_page_title']));
COM_output($display);
