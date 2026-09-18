<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/logs.php                                                            |
// |                                                                           |
// | Read active Geeklog logs from the current site's configured path_log.     |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorAdminNavigation.php';

if (!SEC_hasRights('monitor.admin')) {
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    COM_output(COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30])));
    exit;
}

header('X-Robots-Tag: noindex, nofollow, noarchive', true);

function MONITOR_ACTIVE_LOG_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function MONITOR_ACTIVE_LOG_name($name)
{
    $name = basename((string) $name);
    if ($name === '' || !preg_match('/^[A-Za-z0-9_.-]+\.log$/', $name)) {
        return '';
    }

    return $name;
}

function MONITOR_ACTIVE_LOG_path($name)
{
    global $_CONF;

    $name = MONITOR_ACTIVE_LOG_name($name);
    if ($name === '' || empty($_CONF['path_log'])) {
        return '';
    }

    $base = rtrim((string) $_CONF['path_log'], '/\\') . DIRECTORY_SEPARATOR;
    $path = $base . $name;

    if (!is_file($path) || !is_readable($path)) {
        return '';
    }

    return $path;
}

function MONITOR_ACTIVE_LOG_tail($path, $maxBytes)
{
    $result = array('contents' => '', 'truncated' => false, 'size' => false);

    if (!is_file($path) || !is_readable($path)) {
        return $result;
    }

    $size = @filesize($path);
    $result['size'] = $size;
    if ($size === false || $size <= 0) {
        return $result;
    }

    $handle = @fopen($path, 'rb');
    if ($handle === false) {
        return $result;
    }

    $readBytes = min((int) $size, (int) $maxBytes);
    if ($size > $readBytes) {
        @fseek($handle, -$readBytes, SEEK_END);
        $result['truncated'] = true;
    }

    $contents = @fread($handle, $readBytes);
    @fclose($handle);

    if ($contents !== false) {
        $result['contents'] = $contents;
    }

    return $result;
}

$selected = isset($_GET['log']) ? MONITOR_ACTIVE_LOG_name($_GET['log']) : '';
$selectedPath = $selected !== '' ? MONITOR_ACTIVE_LOG_path($selected) : '';

if ($selectedPath !== '' && isset($_GET['download']) && $_GET['download'] === '1') {
    header('Content-Type: text/plain; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . basename($selectedPath) . '"');
    $size = @filesize($selectedPath);
    if ($size !== false) {
        header('Content-Length: ' . (int) $size);
    }
    readfile($selectedPath);
    exit;
}

$files = array();
if (!empty($_CONF['path_log']) && is_dir($_CONF['path_log'])) {
    $matches = glob(rtrim($_CONF['path_log'], '/\\') . DIRECTORY_SEPARATOR . '*.log');
    if (is_array($matches)) {
        foreach ($matches as $path) {
            if (!is_file($path) || !is_readable($path)) {
                continue;
            }
            $files[] = array(
                'name' => basename($path),
                'size' => @filesize($path),
                'mtime' => @filemtime($path)
            );
        }
    }
}

usort($files, function ($a, $b) {
    return strcmp($a['name'], $b['name']);
});

$content = MONITOR_ADMIN_NAV_render('logs');
$content .= '<div style="padding:13px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:16px">'
          . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_intro'])
          . '<div style="margin-top:7px"><code>'
          . MONITOR_ACTIVE_LOG_h(isset($_CONF['path_log']) ? $_CONF['path_log'] : '')
          . '</code></div></div>';

$baseUrl = rtrim($_CONF['site_admin_url'], '/') . '/plugins/monitor/logs.php';

if (empty($files)) {
    $content .= '<p>' . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_empty']) . '</p>';
} else {
    $content .= '<div style="overflow:auto"><table class="admin-list" style="width:100%;border-collapse:collapse">'
              . '<thead><tr><th>' . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_file']) . '</th>'
              . '<th>' . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_size']) . '</th>'
              . '<th>' . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_modified']) . '</th>'
              . '<th>' . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_actions']) . '</th></tr></thead><tbody>';

    foreach ($files as $entry) {
        $viewUrl = $baseUrl . '?log=' . rawurlencode($entry['name']);
        $downloadUrl = $viewUrl . '&download=1';
        $size = $entry['size'] === false ? '—' : number_format((int) $entry['size']) . ' B';
        $mtime = $entry['mtime'] === false ? '—' : date('Y-m-d H:i:s T', (int) $entry['mtime']);

        $content .= '<tr><td><code>' . MONITOR_ACTIVE_LOG_h($entry['name']) . '</code></td>'
                  . '<td>' . MONITOR_ACTIVE_LOG_h($size) . '</td>'
                  . '<td>' . MONITOR_ACTIVE_LOG_h($mtime) . '</td>'
                  . '<td><a href="' . MONITOR_ACTIVE_LOG_h($viewUrl) . '">'
                  . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_view']) . '</a> &nbsp; '
                  . '<a href="' . MONITOR_ACTIVE_LOG_h($downloadUrl) . '">'
                  . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_download']) . '</a></td></tr>';
    }

    $content .= '</tbody></table></div>';
}

if ($selectedPath !== '') {
    $preview = MONITOR_ACTIVE_LOG_tail($selectedPath, 512 * 1024);
    $content .= '<h3 style="margin-top:24px">' . MONITOR_ACTIVE_LOG_h($selected) . '</h3>';
    if (!empty($preview['truncated'])) {
        $content .= '<div style="padding:9px;border:1px solid #ffe082;background:#fffaf0;border-radius:7px;margin-bottom:10px">'
                  . MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_preview_limited'])
                  . '</div>';
    }
    $content .= '<pre style="max-height:65vh;overflow:auto;padding:12px;border:1px solid #d7dde2;border-radius:7px;background:#fff;white-space:pre-wrap">'
              . MONITOR_ACTIVE_LOG_h($preview['contents'])
              . '</pre>';
}

$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file(array('admin' => 'administration.thtml'));
$T->set_var(array(
    'title' => MONITOR_ACTIVE_LOG_h($LANG_MONITOR_1['logs_active_title']),
    'status_msg' => '',
    'admin_body' => $content
));
$T->parse('output', 'admin');

$body = $T->finish($T->get_var('output'));
$display = COM_createHTMLDocument($body, array('pagetitle' => $LANG_MONITOR_1['logs_active_title']));
COM_output($display);
