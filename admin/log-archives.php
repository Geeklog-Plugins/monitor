<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/log-archives.php                                                    |
// |                                                                           |
// | Browse and retrieve daily Geeklog log archives retained by Monitor.       |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorLogRotation.php';

if (!SEC_hasRights('monitor.admin')) {
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    COM_output(COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30])));
    exit;
}

header('X-Robots-Tag: noindex, nofollow, noarchive', true);

function MONITOR_LOG_ADMIN_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function MONITOR_LOG_ADMIN_bytes($bytes)
{
    if ($bytes === false || $bytes === null || !is_numeric($bytes)) {
        return '—';
    }

    $bytes = (float) $bytes;
    $units = array('B', 'KiB', 'MiB', 'GiB');
    $index = 0;
    while ($bytes >= 1024 && $index < count($units) - 1) {
        $bytes /= 1024;
        $index++;
    }

    return number_format($bytes, $index === 0 ? 0 : 1) . ' ' . $units[$index];
}

$file = isset($_GET['file']) ? basename((string) $_GET['file']) : '';
$archivePath = $file !== '' ? MONITOR_LOG_archivePath($file) : '';

if ($archivePath !== '' && isset($_GET['download']) && $_GET['download'] === '1') {
    header('Content-Type: text/plain; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . basename($archivePath) . '"');
    header('Content-Length: ' . (int) filesize($archivePath));
    readfile($archivePath);
    exit;
}

$archives = MONITOR_LOG_listArchives();
$retentionDays = MONITOR_LOG_retentionDays();
$base = $_CONF['site_admin_url'] . '/plugins/monitor/log-archives.php';

$content = '<div style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0 0 18px 0">'
         . '<a style="display:inline-block;padding:7px 11px;border:1px solid #c7ccd1;border-radius:5px;text-decoration:none" href="'
         . MONITOR_LOG_ADMIN_h($_CONF['site_admin_url'] . '/plugins/monitor/index.php?view=overview') . '">'
         . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['home']) . '</a>'
         . '<a style="display:inline-block;padding:7px 11px;border:1px solid #c7ccd1;border-radius:5px;text-decoration:none;font-weight:bold;background:#eef2f5" href="'
         . MONITOR_LOG_ADMIN_h($base) . '">'
         . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_title']) . '</a>'
         . '<a style="display:inline-block;padding:7px 11px;border:1px solid #c7ccd1;border-radius:5px;text-decoration:none" href="'
         . MONITOR_LOG_ADMIN_h($_CONF['site_admin_url'] . '/logviewer.php') . '">'
         . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['view_logs']) . '</a>'
         . '</div>';

$content .= '<div style="padding:13px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:16px">'
          . MONITOR_LOG_ADMIN_h(sprintf($LANG_MONITOR_1['log_archive_intro'], $retentionDays))
          . '<div style="margin-top:6px;color:#555;font-size:.93em">'
          . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_safety'])
          . '</div></div>';

if (empty($archives)) {
    $content .= '<p>' . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_empty']) . '</p>';
} else {
    $content .= '<div style="overflow:auto"><table class="admin-list" style="width:100%;border-collapse:collapse">'
              . '<thead><tr><th>' . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_date']) . '</th>'
              . '<th>' . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_log']) . '</th>'
              . '<th>' . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_size']) . '</th>'
              . '<th>' . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_actions']) . '</th></tr></thead><tbody>';

    foreach ($archives as $archive) {
        $viewUrl = $base . '?file=' . rawurlencode($archive['file']);
        $downloadUrl = $viewUrl . '&download=1';
        $content .= '<tr><td><strong>' . MONITOR_LOG_ADMIN_h($archive['date']) . '</strong></td>'
                  . '<td><code>' . MONITOR_LOG_ADMIN_h($archive['log']) . '</code></td>'
                  . '<td>' . MONITOR_LOG_ADMIN_h(MONITOR_LOG_ADMIN_bytes($archive['size'])) . '</td>'
                  . '<td><a href="' . MONITOR_LOG_ADMIN_h($viewUrl) . '">'
                  . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_view']) . '</a> &nbsp; '
                  . '<a href="' . MONITOR_LOG_ADMIN_h($downloadUrl) . '">'
                  . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_download']) . '</a></td></tr>';
    }

    $content .= '</tbody></table></div>';
}

if ($archivePath !== '') {
    $size = filesize($archivePath);
    $maxBytes = 512 * 1024;
    $contents = '';
    $truncated = false;

    if ($size !== false && $size > 0) {
        $handle = fopen($archivePath, 'rb');
        if ($handle !== false) {
            $readBytes = min($size, $maxBytes);
            if ($size > $readBytes) {
                fseek($handle, -$readBytes, SEEK_END);
                $truncated = true;
            }
            $contents = fread($handle, $readBytes);
            fclose($handle);
        }
    }

    $content .= '<h3 style="margin-top:24px">' . MONITOR_LOG_ADMIN_h($file) . '</h3>';
    if ($truncated) {
        $content .= '<div style="padding:9px;border:1px solid #ffe082;background:#fffaf0;border-radius:7px;margin-bottom:10px">'
                  . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_preview_limited']) . '</div>';
    }
    $content .= '<pre style="max-height:65vh;overflow:auto;padding:12px;border:1px solid #d7dde2;border-radius:7px;background:#fff;white-space:pre-wrap">'
              . MONITOR_LOG_ADMIN_h($contents === false ? '' : $contents)
              . '</pre>';
}

$T = new Template($_CONF['path'] . 'plugins/monitor/templates');
$T->set_file(array('admin' => 'administration.thtml'));
$T->set_var(array(
    'title' => MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_title']),
    'status_msg' => '',
    'admin_body' => $content
));
$T->parse('output', 'admin');

$body = $T->finish($T->get_var('output'));
$display = COM_createHTMLDocument($body, array('pagetitle' => $LANG_MONITOR_1['log_archive_title']));
COM_output($display);
