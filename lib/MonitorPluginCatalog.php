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

function MONITOR_PLUGIN_CATALOG_token()
{
    global $_MONITOR_CONF;

    $token = getenv('MONITOR_GITHUB_TOKEN');
    if (!is_string($token)) {
        $token = '';
    }
    $token = trim($token);

    if ($token === '' && isset($_MONITOR_CONF['github_token'])) {
        $token = trim((string) $_MONITOR_CONF['github_token']);
    }

    if ($token === '' || preg_match('/\s/', $token)) {
        return '';
    }

    return $token;
}

function MONITOR_PLUGIN_CATALOG_setHttpDiagnostic($diagnostic)
{
    $GLOBALS['MONITOR_PLUGIN_CATALOG_HTTP_DIAGNOSTIC'] = is_array($diagnostic)
        ? $diagnostic : array();
}

function MONITOR_PLUGIN_CATALOG_lastHttpDiagnostic()
{
    return isset($GLOBALS['MONITOR_PLUGIN_CATALOG_HTTP_DIAGNOSTIC'])
        && is_array($GLOBALS['MONITOR_PLUGIN_CATALOG_HTTP_DIAGNOSTIC'])
        ? $GLOBALS['MONITOR_PLUGIN_CATALOG_HTTP_DIAGNOSTIC'] : array();
}

function MONITOR_PLUGIN_CATALOG_setFetchInfo($info)
{
    $GLOBALS['MONITOR_PLUGIN_CATALOG_FETCH_INFO'] = is_array($info)
        ? $info : array();
}

function MONITOR_PLUGIN_CATALOG_lastFetchInfo()
{
    return isset($GLOBALS['MONITOR_PLUGIN_CATALOG_FETCH_INFO'])
        && is_array($GLOBALS['MONITOR_PLUGIN_CATALOG_FETCH_INFO'])
        ? $GLOBALS['MONITOR_PLUGIN_CATALOG_FETCH_INFO'] : array();
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

function MONITOR_PLUGIN_CATALOG_parseHeaders($headers)
{
    $result = array();

    if (!is_array($headers)) {
        return $result;
    }

    foreach ($headers as $header) {
        if (!is_string($header) || strpos($header, ':') === false) {
            continue;
        }

        list($name, $value) = explode(':', $header, 2);
        $name = strtolower(trim($name));
        if ($name !== '') {
            $result[$name] = trim($value);
        }
    }

    return $result;
}

function MONITOR_PLUGIN_CATALOG_httpGetJson($url)
{
    $body = false;
    $status = 0;
    $error = '';
    $headers = array();
    $userAgent = 'Geeklog-Monitor/1.4.0';
    $host = parse_url((string) $url, PHP_URL_HOST);
    $isGitHubApi = is_string($host) && strtolower($host) === 'api.github.com';
    $token = $isGitHubApi ? MONITOR_PLUGIN_CATALOG_token() : '';
    $requestHeaders = array(
        'Accept: application/vnd.github+json',
        'X-GitHub-Api-Version: 2022-11-28'
    );

    if ($token !== '') {
        $requestHeaders[] = 'Authorization: Bearer ' . $token;
    }

    if (function_exists('curl_init')) {
        $handle = curl_init($url);
        if ($handle !== false) {
            curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($handle, CURLOPT_TIMEOUT, 6);
            curl_setopt($handle, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($handle, CURLOPT_HTTPHEADER, $requestHeaders);
            curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($handle, CURLOPT_HEADERFUNCTION, function ($handle, $line) use (&$headers) {
                $length = strlen($line);
                $line = trim($line);
                if ($line !== '' && strpos($line, ':') !== false) {
                    $headers[] = $line;
                }
                return $length;
            });

            $body = curl_exec($handle);
            $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
            if ($body === false) {
                $error = curl_error($handle);
            }
            curl_close($handle);
        } else {
            $error = 'curl_init failed';
        }
    } elseif (ini_get('allow_url_fopen')) {
        $headerText = "User-Agent: {$userAgent}\r\n"
            . implode("\r\n", $requestHeaders) . "\r\n";
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'timeout' => 6,
                'ignore_errors' => true,
                'header' => $headerText
            )
        ));
        $body = @file_get_contents($url, false, $context);

        if (isset($http_response_header) && is_array($http_response_header)) {
            foreach ($http_response_header as $line) {
                if (preg_match('#^HTTP/\S+\s+([0-9]{3})#i', $line, $match)) {
                    $status = (int) $match[1];
                } elseif (strpos($line, ':') !== false) {
                    $headers[] = $line;
                }
            }
        }

        if ($body === false) {
            $error = 'file_get_contents failed';
        }
    } else {
        $error = 'No HTTP transport is available';
    }

    $parsedHeaders = MONITOR_PLUGIN_CATALOG_parseHeaders($headers);
    MONITOR_PLUGIN_CATALOG_setHttpDiagnostic(array(
        'url' => (string) $url,
        'status' => $status,
        'error' => $error,
        'authenticated' => $token !== '',
        'rate_limit' => isset($parsedHeaders['x-ratelimit-limit'])
            ? $parsedHeaders['x-ratelimit-limit'] : '',
        'rate_remaining' => isset($parsedHeaders['x-ratelimit-remaining'])
            ? $parsedHeaders['x-ratelimit-remaining'] : '',
        'rate_reset' => isset($parsedHeaders['x-ratelimit-reset'])
            ? $parsedHeaders['x-ratelimit-reset'] : ''
    ));

    if ($body === false || $body === '' || $status < 200 || $status >= 300) {
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
            MONITOR_PLUGIN_CATALOG_setFetchInfo(array(
                'source' => 'cache',
                'http' => array()
            ));
            return $cached;
        }
    }

    $data = MONITOR_PLUGIN_CATALOG_httpGetJson($url);
    $http = MONITOR_PLUGIN_CATALOG_lastHttpDiagnostic();

    if (is_array($data)) {
        MONITOR_PLUGIN_CATALOG_cacheWrite($cacheKey, $data);
        MONITOR_PLUGIN_CATALOG_setFetchInfo(array(
            'source' => 'remote',
            'http' => $http
        ));
        return $data;
    }

    $stale = MONITOR_PLUGIN_CATALOG_cacheRead($cacheKey, 2592000);
    if (is_array($stale)) {
        MONITOR_PLUGIN_CATALOG_setFetchInfo(array(
            'source' => 'stale_cache',
            'http' => $http
        ));
        return $stale;
    }

    MONITOR_PLUGIN_CATALOG_setFetchInfo(array(
        'source' => 'unavailable',
        'http' => $http
    ));

    return null;
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
        $cached = MONITOR_PLUGIN_CATALOG_cacheRead($cacheKey, 86400);
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

function MONITOR_PLUGIN_CATALOG_isRepositoryList($data)
{
    if (!is_array($data)) {
        return false;
    }

    $index = 0;
    foreach ($data as $key => $repo) {
        if ($key !== $index || !is_array($repo)) {
            return false;
        }
        $index++;
    }

    return true;
}

function MONITOR_PLUGIN_CATALOG_repositories($refresh)
{
    $owner = MONITOR_PLUGIN_CATALOG_owner();
    if ($owner === '') {
        return array(
            'available' => false,
            'owner' => '',
            'repositories' => array(),
            'diagnostic' => array('source' => 'disabled')
        );
    }

    $encodedOwner = rawurlencode($owner);
    $url = 'https://api.github.com/orgs/' . $encodedOwner
        . '/repos?type=public&per_page=100&sort=updated';
    $data = MONITOR_PLUGIN_CATALOG_getJson(
        $url,
        'repositories|' . strtolower($owner),
        43200,
        $refresh
    );
    $diagnostic = MONITOR_PLUGIN_CATALOG_lastFetchInfo();

    if (!MONITOR_PLUGIN_CATALOG_isRepositoryList($data)) {
        $http = isset($diagnostic['http']) && is_array($diagnostic['http'])
            ? $diagnostic['http'] : array();
        $status = isset($http['status']) ? (int) $http['status'] : 0;

        if ($status === 404) {
            $url = 'https://api.github.com/users/' . $encodedOwner
                . '/repos?type=public&per_page=100&sort=updated';
            $data = MONITOR_PLUGIN_CATALOG_getJson(
                $url,
                'repositories-user|' . strtolower($owner),
                43200,
                $refresh
            );
            $diagnostic = MONITOR_PLUGIN_CATALOG_lastFetchInfo();
        }
    }

    if (!MONITOR_PLUGIN_CATALOG_isRepositoryList($data)) {
        return array(
            'available' => false,
            'owner' => $owner,
            'repositories' => array(),
            'diagnostic' => $diagnostic
        );
    }

    $repositories = array();
    foreach ($data as $repo) {
        if (empty($repo['name'])) {
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
        'repositories' => $repositories,
        'diagnostic' => $diagnostic
    );
}

function MONITOR_PLUGIN_CATALOG_normalizeName($name)
{
    return preg_replace('/[^a-z0-9]+/', '', strtolower((string) $name));
}

function MONITOR_PLUGIN_CATALOG_corePlugins()
{
    return array(
        'calendar',
        'links',
        'polls',
        'recaptcha',
        'spamx',
        'staticpages',
        'xmlsitemap'
    );
}

function MONITOR_PLUGIN_CATALOG_isCorePlugin($pluginName)
{
    return in_array(
        strtolower(trim((string) $pluginName)),
        MONITOR_PLUGIN_CATALOG_corePlugins(),
        true
    );
}

function MONITOR_PLUGIN_CATALOG_corePluginUrl($pluginName)
{
    if (!MONITOR_PLUGIN_CATALOG_isCorePlugin($pluginName)) {
        return '';
    }

    return 'https://github.com/Geeklog-Core/geeklog/tree/master/plugins/'
        . rawurlencode(strtolower(trim((string) $pluginName)));
}

function MONITOR_PLUGIN_CATALOG_coreMetadataFromAutoinstall($source)
{
    $source = (string) $source;
    if ($source === '') {
        return null;
    }

    $version = '';
    $geeklog = '';

    if (preg_match("/['\"]pi_version['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $source, $match)) {
        $version = trim((string) $match[1]);
    }
    if (preg_match("/['\"]pi_gl_version['\"]\s*=>\s*['\"]([^'\"]+)['\"]/", $source, $match)) {
        $geeklog = trim((string) $match[1]);
    }

    if ($version === '' && $geeklog === '') {
        return null;
    }

    return array(
        'version' => $version,
        'geeklog_requirement' => $geeklog
    );
}

function MONITOR_PLUGIN_CATALOG_coreLocalMetadata($pluginName)
{
    global $_CONF;

    if (!MONITOR_PLUGIN_CATALOG_isCorePlugin($pluginName) || empty($_CONF['path'])) {
        return null;
    }

    $path = rtrim((string) $_CONF['path'], '/\\')
        . '/plugins/' . strtolower(trim((string) $pluginName))
        . '/autoinstall.php';

    if (!is_file($path) || !is_readable($path)) {
        return null;
    }

    $source = @file_get_contents($path);

    return $source === false
        ? null : MONITOR_PLUGIN_CATALOG_coreMetadataFromAutoinstall($source);
}

function MONITOR_PLUGIN_CATALOG_coreRemoteMetadata($pluginName, $refresh)
{
    $pluginName = strtolower(trim((string) $pluginName));
    if (!MONITOR_PLUGIN_CATALOG_isCorePlugin($pluginName)) {
        return null;
    }

    $url = 'https://api.github.com/repos/Geeklog-Core/geeklog/contents/plugins/'
        . rawurlencode($pluginName) . '/autoinstall.php?ref=master';
    $data = MONITOR_PLUGIN_CATALOG_getJson(
        $url,
        'core-plugin-autoinstall|' . $pluginName . '|master',
        43200,
        $refresh
    );

    if (!is_array($data) || empty($data['content'])) {
        return null;
    }

    $encoding = isset($data['encoding']) ? strtolower((string) $data['encoding']) : '';
    $content = (string) $data['content'];
    if ($encoding === 'base64') {
        $content = base64_decode(str_replace(array("\r", "\n"), '', $content), true);
        if ($content === false) {
            return null;
        }
    }

    $metadata = MONITOR_PLUGIN_CATALOG_coreMetadataFromAutoinstall($content);
    if (!is_array($metadata)) {
        return null;
    }

    $metadata['repository_url'] = MONITOR_PLUGIN_CATALOG_corePluginUrl($pluginName);
    $metadata['source'] = 'geeklog-core';

    return $metadata;
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
        43200,
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
        43200,
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

    /*
     * One tags request is enough to identify the highest semantic version.
     * Avoid the previous releases/latest + tags pair, which doubled API use
     * for every installed plugin without improving version selection.
     */
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
    if (!is_array($repo) || !empty($repo['archived'])) {
        return false;
    }

    $name = isset($repo['name']) ? strtolower((string) $repo['name']) : '';
    $description = isset($repo['description']) ? strtolower((string) $repo['description']) : '';
    $excluded = array(
        '.github',
        'artwork',
        'language-audit',
        'memorandum'
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
