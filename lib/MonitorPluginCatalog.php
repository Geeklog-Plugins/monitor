<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | lib/MonitorPluginCatalog.php                                              |
// |                                                                           |
// | Read-only GitHub metadata for installed and discoverable Geeklog plugins. |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorplugincatalog.php') !== false) {
    die();
}

function MONITOR_PLUGIN_CATALOG_owner()
{
    global $_MONITOR_CONF;

    $owner = isset($_MONITOR_CONF['repository'])
        ? trim((string) $_MONITOR_CONF['repository'])
        : '';

    if ($owner === '' || !preg_match('/^[A-Za-z0-9_.-]+$/', $owner)) {
        return '';
    }

    return $owner;
}

function MONITOR_PLUGIN_CATALOG_cachePath($key)
{
    global $_CONF;

    if (empty($_CONF['path_data']) || !is_dir($_CONF['path_data'])) {
        return '';
    }

    $site = isset($_CONF['site_url']) ? (string) $_CONF['site_url'] : '';
    $suffix = sha1($site . '|' . (string) $key);

    return rtrim($_CONF['path_data'], '/\\')
        . '/monitor-plugin-catalog-' . $suffix . '.json';
}

function MONITOR_PLUGIN_CATALOG_cacheRead($key, $maxAge)
{
    $path = MONITOR_PLUGIN_CATALOG_cachePath($key);
    if ($path === '' || !is_file($path) || !is_readable($path)) {
        return null;
    }

    $mtime = @filemtime($path);
    if ($mtime === false || (time() - $mtime) > (int) $maxAge) {
        return null;
    }

    $raw = @file_get_contents($path);
    if ($raw === false || $raw === '') {
        return null;
    }

    $data = json_decode($raw, true);

    return is_array($data) ? $data : null;
}

function MONITOR_PLUGIN_CATALOG_cacheWrite($key, $data)
{
    $path = MONITOR_PLUGIN_CATALOG_cachePath($key);
    if ($path === '' || !is_array($data)) {
        return;
    }

    $directory = dirname($path);
    if (!is_writable($directory)) {
        return;
    }

    $json = json_encode($data);
    if (!is_string($json) || $json === '') {
        return;
    }

    @file_put_contents($path, $json, LOCK_EX);
}

function MONITOR_PLUGIN_CATALOG_httpGetJson($url)
{
    $body = false;
    $userAgent = 'Geeklog-Monitor/1.4.0';

    if (function_exists('curl_init')) {
        $handle = curl_init($url);
        if ($handle !== false) {
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($handle, CURLOPT_TIMEOUT, 6);
            curl_setopt($handle, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($handle, CURLOPT_HTTPHEADER, array(
                'Accept: application/vnd.github+json'
            ));
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
            $body = curl_exec($handle);
            $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
            curl_close($handle);

            if ($status < 200 || $status >= 300) {
                $body = false;
            }
        }
    } elseif (ini_get('allow_url_fopen')) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 6,
                'header' => "User-Agent: {$userAgent}\r\n"
                    . "Accept: application/vnd.github+json\r\n"
            )
        ));
        $body = @file_get_contents($url, false, $context);
    }

    if ($body === false || $body === '') {
        return null;
    }

    $decoded = json_decode($body, true);

    return is_array($decoded) ? $decoded : null;
}

function MONITOR_PLUGIN_CATALOG_getJson($url, $cacheKey, $maxAge, $refresh)
{
    if (!$refresh) {
        $cached = MONITOR_PLUGIN_CATALOG_cacheRead($cacheKey, $maxAge);
        if (is_array($cached)) {
            return $cached;
        }
    }

    $data = MONITOR_PLUGIN_CATALOG_httpGetJson($url);
    if (is_array($data)) {
        MONITOR_PLUGIN_CATALOG_cacheWrite($cacheKey, $data);
        return $data;
    }

    $stale = MONITOR_PLUGIN_CATALOG_cacheRead($cacheKey, 2592000);

    return is_array($stale) ? $stale : null;
}

function MONITOR_PLUGIN_CATALOG_encodePath($path)
{
    $segments = explode('/', (string) $path);
    foreach ($segments as $index => $segment) {
        $segments[$index] = rawurlencode($segment);
    }

    return implode('/', $segments);
}

function MONITOR_PLUGIN_CATALOG_manifest($owner, $repository, $ref, $refresh)
{
    if ($owner === '' || $repository === '' || $ref === '') {
        return null;
    }

    $cacheKey = 'plugin-manifest|'
        . strtolower($owner . '/' . $repository . '|' . $ref);

    if (!$refresh) {
        $cached = MONITOR_PLUGIN_CATALOG_cacheRead($cacheKey, 21600);
        if (is_array($cached)) {
            return !empty($cached['_missing']) ? null : $cached;
        }
    }

    $url = 'https://raw.githubusercontent.com/'
        . rawurlencode($owner) . '/'
        . rawurlencode($repository) . '/'
        . MONITOR_PLUGIN_CATALOG_encodePath($ref)
        . '/plugin.json';
    $data = MONITOR_PLUGIN_CATALOG_httpGetJson($url);

    if (is_array($data) && isset($data['schema']) && (int) $data['schema'] === 1) {
        MONITOR_PLUGIN_CATALOG_cacheWrite($cacheKey, $data);
        return $data;
    }

    MONITOR_PLUGIN_CATALOG_cacheWrite($cacheKey, array('_missing' => true));

    return null;
}

function MONITOR_PLUGIN_CATALOG_manifestRequirement($manifest, $kind)
{
    if (!is_array($manifest)) {
        return '';
    }

    $kind = strtolower((string) $kind);
    $aliases = $kind === 'php'
        ? array('php', 'php_min', 'php_version', 'min_php')
        : array('geeklog', 'geeklog_min', 'gl_version', 'geeklog_version', 'min_geeklog');
    $containers = array('requires', 'requirements', 'compatibility');

    foreach ($containers as $container) {
        if (!isset($manifest[$container]) || !is_array($manifest[$container])) {
            continue;
        }
        foreach ($aliases as $key) {
            if (isset($manifest[$container][$key]) && is_scalar($manifest[$container][$key])) {
                return trim((string) $manifest[$container][$key]);
            }
        }
    }

    foreach ($aliases as $key) {
        if (isset($manifest[$key]) && is_scalar($manifest[$key])) {
            return trim((string) $manifest[$key]);
        }
    }

    return '';
}

function MONITOR_PLUGIN_CATALOG_repositories($refresh)
{
    $owner = MONITOR_PLUGIN_CATALOG_owner();
    if ($owner === '') {
        return array('available' => false, 'owner' => '', 'repositories' => array());
    }

    $encodedOwner = rawurlencode($owner);
    $url = 'https://api.github.com/orgs/' . $encodedOwner
        . '/repos?type=public&per_page=100&sort=updated';
    $data = MONITOR_PLUGIN_CATALOG_getJson(
        $url,
        'repositories|' . strtolower($owner),
        21600,
        $refresh
    );

    if (!is_array($data)) {
        $url = 'https://api.github.com/users/' . $encodedOwner
            . '/repos?type=public&per_page=100&sort=updated';
        $data = MONITOR_PLUGIN_CATALOG_getJson(
            $url,
            'repositories-user|' . strtolower($owner),
            21600,
            $refresh
        );
    }

    if (!is_array($data)) {
        return array('available' => false, 'owner' => $owner, 'repositories' => array());
    }

    $repositories = array();
    foreach ($data as $repo) {
        if (!is_array($repo) || empty($repo['name'])) {
            continue;
        }

        $name = (string) $repo['name'];
        $repositories[strtolower($name)] = array(
            'name' => $name,
            'url' => isset($repo['html_url']) ? (string) $repo['html_url'] : '',
            'description' => isset($repo['description']) ? (string) $repo['description'] : '',
            'archived' => !empty($repo['archived']),
            'fork' => !empty($repo['fork']),
            'updated_at' => isset($repo['updated_at']) ? (string) $repo['updated_at'] : '',
            'default_branch' => isset($repo['default_branch']) ? (string) $repo['default_branch'] : ''
        );
    }

    return array(
        'available' => true,
        'owner' => $owner,
        'repositories' => $repositories
    );
}

function MONITOR_PLUGIN_CATALOG_normalizeName($name)
{
    return preg_replace('/[^a-z0-9]+/', '', strtolower((string) $name));
}

function MONITOR_PLUGIN_CATALOG_matchRepository($pluginName, $repositories)
{
    if (!is_array($repositories)) {
        return null;
    }

    $exact = strtolower((string) $pluginName);
    if (isset($repositories[$exact])) {
        return $repositories[$exact];
    }

    $normalized = MONITOR_PLUGIN_CATALOG_normalizeName($pluginName);
    if ($normalized === '') {
        return null;
    }

    foreach ($repositories as $repo) {
        if (!is_array($repo) || empty($repo['name'])) {
            continue;
        }
        if (MONITOR_PLUGIN_CATALOG_normalizeName($repo['name']) === $normalized) {
            return $repo;
        }
    }

    return null;
}

function MONITOR_PLUGIN_CATALOG_release($owner, $repository, $refresh)
{
    if ($owner === '' || $repository === '') {
        return null;
    }

    $url = 'https://api.github.com/repos/' . rawurlencode($owner)
        . '/' . rawurlencode($repository) . '/releases/latest';
    $data = MONITOR_PLUGIN_CATALOG_getJson(
        $url,
        'release|' . strtolower($owner . '/' . $repository),
        21600,
        $refresh
    );

    if (!is_array($data) || empty($data['tag_name'])) {
        return null;
    }

    return array(
        'tag' => (string) $data['tag_name'],
        'url' => isset($data['html_url']) ? (string) $data['html_url'] : '',
        'published_at' => isset($data['published_at']) ? (string) $data['published_at'] : '',
        'source' => 'release'
    );
}

function MONITOR_PLUGIN_CATALOG_tags($owner, $repository, $refresh)
{
    if ($owner === '' || $repository === '') {
        return array();
    }

    $url = 'https://api.github.com/repos/' . rawurlencode($owner)
        . '/' . rawurlencode($repository) . '/tags?per_page=30';
    $data = MONITOR_PLUGIN_CATALOG_getJson(
        $url,
        'tags|' . strtolower($owner . '/' . $repository),
        21600,
        $refresh
    );

    return is_array($data) ? $data : array();
}

function MONITOR_PLUGIN_CATALOG_versionFromTag($tag)
{
    $tag = trim((string) $tag);
    if ($tag === '') {
        return '';
    }

    if (preg_match('/([0-9]+(?:\.[0-9]+){1,3}(?:[-+][0-9A-Za-z.-]+)?)/', $tag, $match)) {
        return $match[1];
    }

    return '';
}

function MONITOR_PLUGIN_CATALOG_latestVersion($owner, $repository, $refresh)
{
    $best = null;
    $release = MONITOR_PLUGIN_CATALOG_release($owner, $repository, $refresh);

    if (is_array($release)) {
        $version = MONITOR_PLUGIN_CATALOG_versionFromTag($release['tag']);
        if ($version !== '') {
            $best = array(
                'tag' => $release['tag'],
                'version' => $version,
                'url' => $release['url'],
                'source' => 'release'
            );
        }
    }

    foreach (MONITOR_PLUGIN_CATALOG_tags($owner, $repository, $refresh) as $tag) {
        if (!is_array($tag) || empty($tag['name'])) {
            continue;
        }

        $version = MONITOR_PLUGIN_CATALOG_versionFromTag($tag['name']);
        if ($version === '') {
            continue;
        }

        if ($best === null || version_compare($version, $best['version'], '>')) {
            $best = array(
                'tag' => (string) $tag['name'],
                'version' => $version,
                'url' => 'https://github.com/' . rawurlencode($owner)
                    . '/' . rawurlencode($repository)
                    . '/tree/' . rawurlencode((string) $tag['name']),
                'source' => 'tag'
            );
        }
    }

    return $best;
}

function MONITOR_PLUGIN_CATALOG_versionState($installed, $remoteVersion)
{
    $installed = trim((string) $installed);
    $remote = trim((string) $remoteVersion);

    if ($installed === '' || $remote === '') {
        return 'unknown';
    }

    $comparison = version_compare($installed, $remote);
    if ($comparison < 0) {
        return 'update';
    }
    if ($comparison > 0) {
        return 'ahead';
    }

    return 'current';
}

function MONITOR_PLUGIN_CATALOG_isDiscoverable($repo)
{
    if (!is_array($repo) || !empty($repo['archived']) || !empty($repo['fork'])) {
        return false;
    }

    $name = isset($repo['name']) ? strtolower((string) $repo['name']) : '';
    $description = isset($repo['description']) ? strtolower((string) $repo['description']) : '';
    $excluded = array(
        '.github',
        'artwork',
        'language-audit',
        'memorandum',
        'vthemes'
    );

    if ($name === '' || in_array($name, $excluded, true)) {
        return false;
    }

    if (strpos($description, 'coming soon') !== false) {
        return false;
    }

    return true;
}

function MONITOR_PLUGIN_CATALOG_discoveryState($repo)
{
    $updated = isset($repo['updated_at']) ? strtotime($repo['updated_at']) : false;
    if ($updated === false) {
        return 'unknown';
    }

    $age = time() - $updated;
    if ($age <= 94608000) {
        return 'active';
    }

    return 'legacy';
}
