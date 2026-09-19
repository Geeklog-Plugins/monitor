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
require_once $_CONF['path_system'] . 'lib-admin.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorLogRotation.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorAdminNavigation.php';

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

function MONITOR_LOG_ADMIN_sortValue($archive, $field)
{
    if ($field === 'log') {
        return isset($archive['log']) ? strtolower((string) $archive['log']) : '';
    }
    if ($field === 'size') {
        return isset($archive['size']) && is_numeric($archive['size'])
            ? (int) $archive['size'] : -1;
    }

    return isset($archive['date']) ? (string) $archive['date'] : '';
}

function MONITOR_LOG_ADMIN_sortArchives(&$archives, $field, $direction)
{
    usort($archives, function ($a, $b) use ($field, $direction) {
        $left = MONITOR_LOG_ADMIN_sortValue($a, $field);
        $right = MONITOR_LOG_ADMIN_sortValue($b, $field);

        if ($left == $right) {
            $leftFile = isset($a['file']) ? (string) $a['file'] : '';
            $rightFile = isset($b['file']) ? (string) $b['file'] : '';
            $result = strcasecmp($leftFile, $rightFile);
        } elseif ($field === 'size') {
            $result = ($left < $right) ? -1 : 1;
        } else {
            $result = strcasecmp((string) $left, (string) $right);
        }

        return ($direction === 'asc') ? $result : -$result;
    });
}

function MONITOR_LOG_ADMIN_sortHeader($label, $field, $currentField, $currentDirection, $base)
{
    $direction = ($currentField === $field && $currentDirection === 'asc')
        ? 'desc' : 'asc';
    $indicator = '';
    if ($currentField === $field) {
        $indicator = ($currentDirection === 'asc') ? ' ▲' : ' ▼';
    }

    $url = $base . '?sort=' . rawurlencode($field)
         . '&order=' . rawurlencode($direction);

    return '<a href="' . MONITOR_LOG_ADMIN_h($url) . '">'
         . MONITOR_LOG_ADMIN_h($label . $indicator) . '</a>';
}

function MONITOR_LOG_ADMIN_listField($fieldname, $fieldvalue, $A, $icon_arr)
{
    if ($fieldname === 'date') {
        return '<strong>' . MONITOR_LOG_ADMIN_h($fieldvalue) . '</strong>';
    }
    if ($fieldname === 'log') {
        return '<code>' . MONITOR_LOG_ADMIN_h($fieldvalue) . '</code>';
    }
    if ($fieldname === 'size') {
        return MONITOR_LOG_ADMIN_h(MONITOR_LOG_ADMIN_bytes($fieldvalue));
    }
    if ($fieldname === 'actions') {
        return isset($A['actions']) ? $A['actions'] : '';
    }

    return MONITOR_LOG_ADMIN_h($fieldvalue);
}

$file = isset($_GET['file']) ? basename((string) $_GET['file']) : '';
$archivePath = $file !== '' ? MONITOR_LOG_archivePath($file) : '';
$base = $_CONF['site_admin_url'] . '/plugins/monitor/log-archives.php';

if ($archivePath !== '' && isset($_GET['download']) && $_GET['download'] === '1') {
    header('Content-Type: text/plain; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . basename($archivePath) . '"');
    header('Content-Length: ' . (int) filesize($archivePath));
    readfile($archivePath);
    exit;
}

$content = MONITOR_ADMIN_NAV_render('log_archives');

/*
 * A selected archive is a dedicated detail view. The list opens it in a new
 * browser tab/window and this page provides an explicit route back to the
 * sortable archive catalogue.
 */
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

    $content .= '<p><a href="' . MONITOR_LOG_ADMIN_h($base) . '">&larr; '
              . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_back'])
              . '</a></p>'
              . '<h3>' . MONITOR_LOG_ADMIN_h($file) . '</h3>';

    if ($truncated) {
        $content .= '<div style="padding:9px;border:1px solid #ffe082;background:#fffaf0;border-radius:7px;margin-bottom:10px">'
                  . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_preview_limited']) . '</div>';
    }

    $content .= '<pre style="max-height:70vh;overflow:auto;padding:12px;border:1px solid #d7dde2;border-radius:7px;background:#fff;white-space:pre-wrap">'
              . MONITOR_LOG_ADMIN_h($contents === false ? '' : $contents)
              . '</pre>'
              . '<p><a href="' . MONITOR_LOG_ADMIN_h($base) . '">&larr; '
              . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_back'])
              . '</a></p>';
} else {
    $archives = MONITOR_LOG_listArchives();
    $retentionDays = MONITOR_LOG_retentionDays();

    $sort = isset($_GET['sort']) ? strtolower((string) $_GET['sort']) : 'date';
    if (!in_array($sort, array('date', 'log', 'size'), true)) {
        $sort = 'date';
    }

    $order = isset($_GET['order']) ? strtolower((string) $_GET['order']) : 'desc';
    if ($order !== 'asc' && $order !== 'desc') {
        $order = 'desc';
    }

    $content .= '<div style="padding:13px;border:1px solid #d7dde2;border-radius:8px;background:#fafbfc;margin-bottom:16px">'
              . MONITOR_LOG_ADMIN_h(sprintf($LANG_MONITOR_1['log_archive_intro'], $retentionDays))
              . '<div style="margin-top:6px;color:#555;font-size:.93em">'
              . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_safety'])
              . '</div></div>';

    if (empty($archives)) {
        $content .= '<p>' . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_empty']) . '</p>';
    } else {
        MONITOR_LOG_ADMIN_sortArchives($archives, $sort, $order);

        $data = array();
        foreach ($archives as $archive) {
            $viewUrl = $base . '?file=' . rawurlencode($archive['file']);
            $downloadUrl = $viewUrl . '&download=1';
            $data[] = array(
                'date' => isset($archive['date']) ? $archive['date'] : '',
                'log' => isset($archive['log']) ? $archive['log'] : '',
                'size' => isset($archive['size']) ? $archive['size'] : null,
                'actions' => '<a href="' . MONITOR_LOG_ADMIN_h($viewUrl)
                    . '" target="_blank" rel="noopener noreferrer">'
                    . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_view'])
                    . '</a> &nbsp; <a href="' . MONITOR_LOG_ADMIN_h($downloadUrl)
                    . '">' . MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_download'])
                    . '</a>'
            );
        }

        $header = array(
            array(
                'text' => MONITOR_LOG_ADMIN_sortHeader(
                    $LANG_MONITOR_1['log_archive_date'], 'date', $sort, $order, $base
                ),
                'field' => 'date'
            ),
            array(
                'text' => MONITOR_LOG_ADMIN_sortHeader(
                    $LANG_MONITOR_1['log_archive_log'], 'log', $sort, $order, $base
                ),
                'field' => 'log'
            ),
            array(
                'text' => MONITOR_LOG_ADMIN_sortHeader(
                    $LANG_MONITOR_1['log_archive_size'], 'size', $sort, $order, $base
                ),
                'field' => 'size'
            ),
            array(
                'text' => MONITOR_LOG_ADMIN_h($LANG_MONITOR_1['log_archive_actions']),
                'field' => 'actions'
            )
        );

        $text = array(
            'has_menu' => false,
            'has_extras' => false,
            'title' => '',
            'no_data' => $LANG_MONITOR_1['log_archive_empty'],
            'form_url' => $base
        );

        $content .= ADMIN_simpleList(
            'MONITOR_LOG_ADMIN_listField',
            $header,
            $text,
            $data
        );
    }
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
