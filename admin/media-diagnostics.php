<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | media-diagnostics.php                                                     |
// |                                                                           |
// | Read-only JSON details for the oversized-image health diagnostic.         |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorMediaDiagnostics.php';

header('Content-Type: application/json; charset=UTF-8');
header('X-Robots-Tag: noindex, nofollow, noarchive');

if (!SEC_hasRights('monitor.admin')) {
    http_response_code(403);
    echo json_encode(array(
        'ok' => false,
        'error' => 'access_denied'
    ));
    exit;
}

$media = MONITOR_MEDIA_oversizedImages(2000, 20);
$items = array();

if (!empty($media['available']) && !empty($media['items'])) {
    foreach ($media['items'] as $item) {
        $items[] = array(
            'file' => isset($item['file']) ? (string) $item['file'] : '',
            'relative_path' => isset($item['relative_path']) ? (string) $item['relative_path'] : '',
            'public_url' => isset($item['public_url']) ? (string) $item['public_url'] : '',
            'width' => isset($item['width']) ? (int) $item['width'] : 0,
            'height' => isset($item['height']) ? (int) $item['height'] : 0,
            'size_bytes' => isset($item['size_bytes']) ? (int) $item['size_bytes'] : 0,
            'dimension_issue' => !empty($item['dimension_issue']),
            'size_issue' => !empty($item['size_issue'])
        );
    }
}

/*
 * Match the File Manager entry exposed by Geeklog 2.1.1 and 2.2.2 admin UI.
 * Type=Root opens the manager at the complete file tree instead of restricting
 * the view to a file-picker mode such as Type=File.
 */
$fileManager = isset($_CONF['site_url'])
    ? rtrim($_CONF['site_url'], '/') . '/filemanager/index.php?Type=Root'
    : '/filemanager/index.php?Type=Root';

$labels = array(
    'oversized_single' => isset($LANG_MONITOR_1['media_oversized_single'])
        ? $LANG_MONITOR_1['media_oversized_single'] : '1 image exceeds the recommended limits.',
    'oversized_multiple' => isset($LANG_MONITOR_1['media_oversized_multiple'])
        ? $LANG_MONITOR_1['media_oversized_multiple'] : '%d images exceed the recommended limits.',
    'show_files' => isset($LANG_MONITOR_1['media_show_files'])
        ? $LANG_MONITOR_1['media_show_files'] : 'Show files (%d)',
    'hide_files' => isset($LANG_MONITOR_1['media_hide_files'])
        ? $LANG_MONITOR_1['media_hide_files'] : 'Hide files (%d)',
    'open_file_manager' => isset($LANG_MONITOR_1['media_open_file_manager'])
        ? $LANG_MONITOR_1['media_open_file_manager'] : 'Open File Manager',
    'view_image' => isset($LANG_MONITOR_1['media_view_image'])
        ? $LANG_MONITOR_1['media_view_image'] : 'View image',
    'more_files' => isset($LANG_MONITOR_1['media_more_files'])
        ? $LANG_MONITOR_1['media_more_files'] : 'Additional oversized images exist; the list is limited.',
    'partial_scan' => isset($LANG_MONITOR_1['media_partial_scan'])
        ? $LANG_MONITOR_1['media_partial_scan'] : 'The filesystem scan reached its safety limit.'
);

echo json_encode(array(
    'ok' => !empty($media['available']),
    'checked' => isset($media['checked']) ? (int) $media['checked'] : 0,
    'total' => isset($media['total']) ? (int) $media['total'] : 0,
    'partial_scan' => !empty($media['partial']),
    'items_truncated' => !empty($media['items_truncated']),
    'items' => $items,
    'file_manager_url' => $fileManager,
    'threshold_dimension' => 1600,
    'threshold_bytes' => 2 * 1024 * 1024,
    'labels' => $labels
));
