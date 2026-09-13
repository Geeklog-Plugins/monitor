<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/plugin-icons.php                                                    |
// |                                                                           |
// | Read-only icon resolver for the Monitor plugin catalog.                   |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorPluginCatalog.php';

header('Content-Type: application/json; charset=utf-8');

if (!SEC_hasRights('monitor.admin')) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'icons' => array()));
    exit;
}

function MONITOR_PLUGIN_ICONS_normalize($name)
{
    return MONITOR_PLUGIN_CATALOG_normalizeName($name);
}

function MONITOR_PLUGIN_ICONS_safePluginId($name)
{
    return preg_match('/^[A-Za-z0-9_.-]+$/', (string) $name) === 1;
}

function MONITOR_PLUGIN_ICONS_safeIconPath($path)
{
    $path = str_replace('\\', '/', trim((string) $path));

    if ($path === '' || $path[0] === '/' || strpos($path, '://') !== false) {
        return '';
    }

    $segments = explode('/', $path);
    foreach ($segments as $segment) {
        if ($segment === '' || $segment === '.' || $segment === '..') {
            return '';
        }
    }

    if (!preg_match('/\.(?:png|jpe?g|gif|svg|webp)$/i', $path)) {
        return '';
    }

    return $path;
}

function MONITOR_PLUGIN_ICONS_safeRuntimeUrl($url)
{
    $url = trim((string) $url);
    if ($url === '' || strpos($url, '<') !== false || strpos($url, '>') !== false) {
        return '';
    }

    if (preg_match('#^(?:https?:)?//#i', $url) || $url[0] === '/') {
        return $url;
    }

    return '';
}

function MONITOR_PLUGIN_ICONS_encodePath($path)
{
    $segments = explode('/', (string) $path);
    foreach ($segments as $index => $segment) {
        $segments[$index] = rawurlencode($segment);
    }

    return implode('/', $segments);
}

function MONITOR_PLUGIN_ICONS_fallbackUrl()
{
    global $_CONF;

    return rtrim($_CONF['site_admin_url'], '/')
        . '/plugins/monitor/images/unavailable.png';
}

function MONITOR_PLUGIN_ICONS_readLocalManifest($pluginName)
{
    global $_CONF;

    if (!MONITOR_PLUGIN_ICONS_safePluginId($pluginName)) {
        return null;
    }

    $path = rtrim($_CONF['path'], '/\\')
        . '/plugins/' . $pluginName . '/plugin.json';

    if (!is_file($path) || !is_readable($path)) {
        return null;
    }

    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return null;
    }

    $data = json_decode($raw, true);
    if (!is_array($data) || !isset($data['schema']) || (int) $data['schema'] !== 1) {
        return null;
    }

    return $data;
}

function MONITOR_PLUGIN_ICONS_localUrl($pluginName, $manifest)
{
    global $_CONF;

    if (!is_array($manifest) || empty($manifest['icon'])
            || !MONITOR_PLUGIN_ICONS_safePluginId($pluginName)) {
        return '';
    }

    $icon = MONITOR_PLUGIN_ICONS_safeIconPath($manifest['icon']);
    if ($icon === '') {
        return '';
    }

    $encodedPlugin = rawurlencode($pluginName);

    if (strpos($icon, 'admin/') === 0) {
        return rtrim($_CONF['site_admin_url'], '/')
            . '/plugins/' . $encodedPlugin . '/'
            . MONITOR_PLUGIN_ICONS_encodePath(substr($icon, 6));
    }

    if (strpos($icon, 'public_html/') === 0) {
        return rtrim($_CONF['site_url'], '/')
            . '/' . $encodedPlugin . '/'
            . MONITOR_PLUGIN_ICONS_encodePath(substr($icon, 12));
    }

    return '';
}

function MONITOR_PLUGIN_ICONS_remoteManifest($owner, $repo, $branch, $refresh)
{
    if ($owner === '' || $repo === '' || $branch === '') {
        return null;
    }

    $cacheKey = 'plugin-manifest|'
        . strtolower($owner . '/' . $repo . '|' . $branch);

    if (!$refresh) {
        $cached = MONITOR_PLUGIN_CATALOG_cacheRead($cacheKey, 21600);
        if (is_array($cached)) {
            if (!empty($cached['_missing'])) {
                return null;
            }
            return $cached;
        }
    }

    $url = 'https://raw.githubusercontent.com/'
        . rawurlencode($owner) . '/'
        . rawurlencode($repo) . '/'
        . MONITOR_PLUGIN_ICONS_encodePath($branch)
        . '/plugin.json';

    $data = MONITOR_PLUGIN_CATALOG_httpGetJson($url);

    if (is_array($data) && isset($data['schema']) && (int) $data['schema'] === 1) {
        MONITOR_PLUGIN_CATALOG_cacheWrite($cacheKey, $data);
        return $data;
    }

    MONITOR_PLUGIN_CATALOG_cacheWrite($cacheKey, array('_missing' => true));

    return null;
}

function MONITOR_PLUGIN_ICONS_remoteUrl($owner, $repo, $branch, $manifest)
{
    if (!is_array($manifest) || empty($manifest['icon'])) {
        return '';
    }

    $icon = MONITOR_PLUGIN_ICONS_safeIconPath($manifest['icon']);
    if ($icon === '') {
        return '';
    }

    return 'https://raw.githubusercontent.com/'
        . rawurlencode($owner) . '/'
        . rawurlencode($repo) . '/'
        . MONITOR_PLUGIN_ICONS_encodePath($branch) . '/'
        . MONITOR_PLUGIN_ICONS_encodePath($icon);
}

function MONITOR_PLUGIN_ICONS_runtimeUrl($pluginName, $enabled)
{
    if (!$enabled || !function_exists('PLG_getIcon')) {
        return '';
    }

    $url = PLG_getIcon($pluginName);

    return MONITOR_PLUGIN_ICONS_safeRuntimeUrl($url);
}

function MONITOR_PLUGIN_ICONS_resolveInstalled($pluginName, $enabled, $owner, $repo, $refresh)
{
    $runtime = MONITOR_PLUGIN_ICONS_runtimeUrl($pluginName, $enabled);
    if ($runtime !== '') {
        return $runtime;
    }

    $localManifest = MONITOR_PLUGIN_ICONS_readLocalManifest($pluginName);
    $local = MONITOR_PLUGIN_ICONS_localUrl($pluginName, $localManifest);
    if ($local !== '') {
        return $local;
    }

    if (is_array($repo)) {
        $repoName = isset($repo['name']) ? (string) $repo['name'] : '';
        $branch = isset($repo['default_branch']) ? (string) $repo['default_branch'] : '';
        $remoteManifest = MONITOR_PLUGIN_ICONS_remoteManifest(
            $owner,
            $repoName,
            $branch,
            $refresh
        );
        $remote = MONITOR_PLUGIN_ICONS_remoteUrl(
            $owner,
            $repoName,
            $branch,
            $remoteManifest
        );
        if ($remote !== '') {
            return $remote;
        }
    }

    return MONITOR_PLUGIN_ICONS_fallbackUrl();
}

function MONITOR_PLUGIN_ICONS_resolveRepository($owner, $repo, $refresh)
{
    if (!is_array($repo)) {
        return MONITOR_PLUGIN_ICONS_fallbackUrl();
    }

    $repoName = isset($repo['name']) ? (string) $repo['name'] : '';
    $branch = isset($repo['default_branch']) ? (string) $repo['default_branch'] : '';
    $manifest = MONITOR_PLUGIN_ICONS_remoteManifest(
        $owner,
        $repoName,
        $branch,
        $refresh
    );
    $remote = MONITOR_PLUGIN_ICONS_remoteUrl(
        $owner,
        $repoName,
        $branch,
        $manifest
    );

    return $remote !== '' ? $remote : MONITOR_PLUGIN_ICONS_fallbackUrl();
}

$refresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';
$catalog = MONITOR_PLUGIN_CATALOG_repositories($refresh);
$owner = isset($catalog['owner']) ? (string) $catalog['owner'] : '';
$repositories = isset($catalog['repositories']) && is_array($catalog['repositories'])
    ? $catalog['repositories'] : array();

$icons = array();
$installedNames = array();

$result = DB_query(
    "SELECT pi_name, pi_enabled FROM {$_TABLES['plugins']} ORDER BY pi_name"
);

if ($result) {
    while ($row = DB_fetchArray($result)) {
        $pluginName = isset($row['pi_name']) ? (string) $row['pi_name'] : '';
        if ($pluginName === '') {
            continue;
        }

        $normalized = MONITOR_PLUGIN_ICONS_normalize($pluginName);
        if ($normalized === '') {
            continue;
        }

        $repo = MONITOR_PLUGIN_CATALOG_matchRepository($pluginName, $repositories);
        $installedNames[$normalized] = true;
        $icons[$normalized] = MONITOR_PLUGIN_ICONS_resolveInstalled(
            $pluginName,
            !empty($row['pi_enabled']),
            $owner,
            $repo,
            $refresh
        );
    }
}

foreach ($repositories as $repo) {
    if (!MONITOR_PLUGIN_CATALOG_isDiscoverable($repo)) {
        continue;
    }

    $normalized = MONITOR_PLUGIN_ICONS_normalize(
        isset($repo['name']) ? $repo['name'] : ''
    );

    if ($normalized === '' || isset($installedNames[$normalized])) {
        continue;
    }

    $icons[$normalized] = MONITOR_PLUGIN_ICONS_resolveRepository(
        $owner,
        $repo,
        $refresh
    );
}

echo json_encode(array(
    'ok' => true,
    'fallback' => MONITOR_PLUGIN_ICONS_fallbackUrl(),
    'icons' => $icons
));
