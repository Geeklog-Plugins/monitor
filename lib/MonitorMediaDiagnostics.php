<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorMediaDiagnostics.php                                               |
// |                                                                           |
// | Read-only helpers for locating oversized images reported by Monitor.      |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitormediadiagnostics.php') !== false) {
    die('This file can not be used on its own.');
}

/**
 * Return a bounded list of oversized images below Geeklog's path_images.
 *
 * Only paths relative to path_images are returned. Absolute filesystem paths
 * never leave this helper. No file is modified.
 *
 * @param int $maxFiles Maximum supported images to inspect
 * @param int $maxItems Maximum oversized items to return
 * @return array
 */
function MONITOR_MEDIA_oversizedImages($maxFiles, $maxItems)
{
    global $_CONF;

    $root = isset($_CONF['path_images']) ? $_CONF['path_images'] : '';
    $maxFiles = max(1, min(5000, (int) $maxFiles));
    $maxItems = max(1, min(100, (int) $maxItems));
    $maxDimension = 1600;
    $maxBytes = 2 * 1024 * 1024;

    $result = array(
        'available' => false,
        'checked' => 0,
        'total' => 0,
        'partial' => false,
        'items_truncated' => false,
        'items' => array()
    );

    if ($root === '' || !is_dir($root) || !is_readable($root)) {
        return $result;
    }

    $result['available'] = true;
    $root = rtrim($root, '/\\') . DIRECTORY_SEPARATOR;
    $extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp');

    try {
        $directory = new RecursiveDirectoryIterator(
            $root,
            FilesystemIterator::SKIP_DOTS
        );
        $iterator = new RecursiveIteratorIterator(
            $directory,
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $fileInfo) {
            if ($result['checked'] >= $maxFiles) {
                $result['partial'] = true;
                break;
            }

            if (!$fileInfo->isFile() || $fileInfo->isLink()) {
                continue;
            }

            $extension = strtolower($fileInfo->getExtension());
            if (!in_array($extension, $extensions, true)) {
                continue;
            }

            $result['checked']++;
            $path = $fileInfo->getPathname();
            $size = $fileInfo->getSize();
            $dimensions = @getimagesize($path);
            if ($dimensions === false) {
                continue;
            }

            $width = isset($dimensions[0]) ? (int) $dimensions[0] : 0;
            $height = isset($dimensions[1]) ? (int) $dimensions[1] : 0;
            $dimensionIssue = ($width > $maxDimension || $height > $maxDimension);
            $sizeIssue = ($size > $maxBytes);

            if (!$dimensionIssue && !$sizeIssue) {
                continue;
            }

            $result['total']++;
            if (count($result['items']) >= $maxItems) {
                $result['items_truncated'] = true;
                continue;
            }

            $relative = substr($path, strlen($root));
            $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);

            $result['items'][] = array(
                'file' => basename($path),
                'relative_path' => $relative,
                'width' => $width,
                'height' => $height,
                'size_bytes' => (int) $size,
                'dimension_issue' => $dimensionIssue,
                'size_issue' => $sizeIssue
            );
        }
    } catch (Exception $e) {
        $result['available'] = false;
    }

    usort($result['items'], function ($a, $b) {
        $left = isset($a['size_bytes']) ? (int) $a['size_bytes'] : 0;
        $right = isset($b['size_bytes']) ? (int) $b['size_bytes'] : 0;
        if ($left === $right) {
            return strcmp($a['relative_path'], $b['relative_path']);
        }

        return ($left > $right) ? -1 : 1;
    });

    return $result;
}
