<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | admin/plugin-icons.php                                                    |
// |                                                                           |
// | Read-only icon and plugin.json metadata resolver for the Monitor catalog. |
// +---------------------------------------------------------------------------+

require_once '../../../lib-common.php';
require_once '../../auth.inc.php';
require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorPluginCatalog.php';

header('Content-Type: application/json; charset=utf-8');

if (!SEC_hasRights('monitor.admin')) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'icons' => array(), 'metadata' => array()));
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
    return MONITOR_PLUGIN_CATALOG_encodePath($path);
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

function MONITOR_PLUGIN_ICONS_corePlugins()
{
    return array(
        'calendar' => array('file' => 'calendar/images/calendar.png', 'url' => 'calendar/images/calendar.png', 'source' => 'public_html/calendar/images/calendar.png'),
        'links' => array('file' => 'links/images/links.png', 'url' => 'links/images/links.png', 'source' => 'public_html/links/images/links.png'),
        'polls' => array('file' => 'polls/images/polls.png', 'url' => 'polls/images/polls.png', 'source' => 'public_html/polls/images/polls.png'),
        'recaptcha' => array('file' => 'admin/plugins/recaptcha/images/recaptcha.png', 'url' => 'admin/plugins/recaptcha/images/recaptcha.png', 'source' => 'public_html/admin/plugins/recaptcha/images/recaptcha.png'),
        'spamx' => array('file' => 'admin/plugins/spamx/images/spamx.png', 'url' => 'admin/plugins/spamx/images/spamx.png', 'source' => 'public_html/admin/plugins/spamx/images/spamx.png'),
        'staticpages' => array('file' => 'staticpages/images/staticpages.png', 'url' => 'staticpages/images/staticpages.png', 'source' => 'public_html/staticpages/images/staticpages.png'),
        'xmlsitemap' => array('file' => 'xmlsitemap/images/xmlsitemap.png', 'url' => 'xmlsitemap/images/xmlsitemap.png', 'source' => 'public_html/xmlsitemap/images/xmlsitemap.png')
    );
}

function MONITOR_PLUGIN_ICONS_coreLocalUrl($pluginName)
{
    global $_CONF;

    $pluginName = strtolower((string) $pluginName);
    $plugins = MONITOR_PLUGIN_ICONS_corePlugins();
    if (!isset($plugins[$pluginName]) || empty($_CONF['path_html'])) {
        return '';
    }

    $definition = $plugins[$pluginName];
    $file = rtrim($_CONF['path_html'], '/\\') . '/' . $definition['file'];
    if (!is_file($file)) {
        return '';
    }

    return rtrim($_CONF['site_url'], '/') . '/' . MONITOR_PLUGIN_ICONS_encodePath($definition['url']);
}

function MONITOR_PLUGIN_ICONS_coreRemoteUrl($pluginName)
{
    $pluginName = strtolower((string) $pluginName);
    $plugins = MONITOR_PLUGIN_ICONS_corePlugins();
    if (!isset($plugins[$pluginName])) {
        return '';
    }

    return 'https://raw.githubusercontent.com/Geeklog-Core/geeklog/master/'
        . MONITOR_PLUGIN_ICONS_encodePath($plugins[$pluginName]['source']);
}

function MONITOR_PLUGIN_ICONS_remoteManifest($owner, $repo, $branch, $refresh)
{
    return MONITOR_PLUGIN_CATALOG_manifest($owner, $repo, $branch, $refresh);
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

    return MONITOR_PLUGIN_ICONS_safeRuntimeUrl(PLG_getIcon($pluginName));
}

function MONITOR_PLUGIN_ICONS_requirementVersion($value)
{
    if (preg_match('/([0-9]+(?:\.[0-9]+){1,3})/', trim((string) $value), $match)) {
        return $match[1];
    }
    return '';
}

function MONITOR_PLUGIN_ICONS_requirementState($current, $required)
{
    $current = MONITOR_PLUGIN_ICONS_requirementVersion($current);
    $required = MONITOR_PLUGIN_ICONS_requirementVersion($required);
    if ($current === '' || $required === '') {
        return 'unknown';
    }
    return version_compare($current, $required, '>=') ? 'compatible' : 'incompatible';
}

function MONITOR_PLUGIN_ICONS_metadata($manifest, $source)
{
    global $_CONF;

    if (!is_array($manifest)) {
        return array();
    }

    $geeklog = MONITOR_PLUGIN_CATALOG_manifestRequirement($manifest, 'geeklog');
    $php = MONITOR_PLUGIN_CATALOG_manifestRequirement($manifest, 'php');
    $currentGeeklog = defined('VERSION') ? (string) VERSION
        : (isset($_CONF['version']) ? (string) $_CONF['version'] : '');
    $geeklogState = $geeklog !== ''
        ? MONITOR_PLUGIN_ICONS_requirementState($currentGeeklog, $geeklog) : 'not_declared';
    $phpState = $php !== ''
        ? MONITOR_PLUGIN_ICONS_requirementState(PHP_VERSION, $php) : 'not_declared';
    $declared = 0;
    $unknown = false;
    $overall = 'unknown';

    foreach (array(
        array($geeklog, $geeklogState),
        array($php, $phpState)
    ) as $requirement) {
        if ($requirement[0] === '') {
            continue;
        }
        $declared++;
        if ($requirement[1] === 'incompatible') {
            $overall = 'incompatible';
            $unknown = false;
            break;
        }
        if ($requirement[1] === 'unknown') {
            $unknown = true;
        }
    }

    if ($overall !== 'incompatible' && $declared > 0) {
        $overall = $unknown ? 'unknown' : 'compatible';
    }

    return array(
        'id' => isset($manifest['id']) && is_scalar($manifest['id']) ? trim((string) $manifest['id']) : '',
        'name' => isset($manifest['name']) && is_scalar($manifest['name']) ? trim((string) $manifest['name']) : '',
        'requires' => array('geeklog' => $geeklog, 'php' => $php),
        'compatibility' => array(
            'state' => $overall,
            'geeklog' => $geeklogState,
            'php' => $phpState,
            'geeklog_current' => $currentGeeklog,
            'php_current' => PHP_VERSION
        ),
        'requirements_declared' => $declared > 0,
        'manifest_available' => true,
        'source' => $source
    );
}

function MONITOR_PLUGIN_ICONS_resolveInstalled($pluginName, $enabled, $owner, $repo, $refresh, &$manifest, &$source)
{
    $manifest = MONITOR_PLUGIN_ICONS_readLocalManifest($pluginName);
    $source = is_array($manifest) ? 'local_plugin_json' : '';

    $local = MONITOR_PLUGIN_ICONS_localUrl($pluginName, $manifest);
    if ($local !== '') {
        return $local;
    }

    $runtime = MONITOR_PLUGIN_ICONS_runtimeUrl($pluginName, $enabled);
    if ($runtime !== '') {
        return $runtime;
    }

    $coreLocal = MONITOR_PLUGIN_ICONS_coreLocalUrl($pluginName);
    if ($coreLocal !== '') {
        return $coreLocal;
    }

    if (is_array($repo)) {
        $repoName = isset($repo['name']) ? (string) $repo['name'] : '';
        $branch = isset($repo['default_branch']) ? (string) $repo['default_branch'] : '';
        $remoteManifest = MONITOR_PLUGIN_ICONS_remoteManifest($owner, $repoName, $branch, $refresh);
        $remote = MONITOR_PLUGIN_ICONS_remoteUrl($owner, $repoName, $branch, $remoteManifest);
        if ($remote !== '') {
            if (!is_array($manifest) && is_array($remoteManifest)) {
                $manifest = $remoteManifest;
                $source = 'remote_plugin_json';
            }
            return $remote;
        }
    }

    $coreRemote = MONITOR_PLUGIN_ICONS_coreRemoteUrl($pluginName);
    if ($coreRemote !== '') {
        return $coreRemote;
    }

    return MONITOR_PLUGIN_ICONS_fallbackUrl();
}

function MONITOR_PLUGIN_ICONS_resolveRepository($owner, $repo, $refresh, &$manifest)
{
    $manifest = null;
    if (!is_array($repo)) {
        return MONITOR_PLUGIN_ICONS_fallbackUrl();
    }

    $repoName = isset($repo['name']) ? (string) $repo['name'] : '';
    $branch = isset($repo['default_branch']) ? (string) $repo['default_branch'] : '';
    $manifest = MONITOR_PLUGIN_ICONS_remoteManifest($owner, $repoName, $branch, $refresh);
    $remote = MONITOR_PLUGIN_ICONS_remoteUrl($owner, $repoName, $branch, $manifest);

    return $remote !== '' ? $remote : MONITOR_PLUGIN_ICONS_fallbackUrl();
}

$refresh = isset($_GET['refresh']) && $_GET['refresh'] === '1';
$catalog = MONITOR_PLUGIN_CATALOG_repositories($refresh);
$owner = isset($catalog['owner']) ? (string) $catalog['owner'] : '';
$repositories = isset($catalog['repositories']) && is_array($catalog['repositories'])
    ? $catalog['repositories'] : array();

$icons = array();
$metadata = array();
$installedNames = array();

$result = DB_query("SELECT pi_name, pi_enabled FROM {$_TABLES['plugins']} ORDER BY pi_name");

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
        $manifest = null;
        $source = '';
        $installedNames[$normalized] = true;
        $icons[$normalized] = MONITOR_PLUGIN_ICONS_resolveInstalled(
            $pluginName,
            !empty($row['pi_enabled']),
            $owner,
            $repo,
            $refresh,
            $manifest,
            $source
        );
        if (is_array($manifest)) {
            $metadata[$normalized] = MONITOR_PLUGIN_ICONS_metadata($manifest, $source);
        }
    }
}

foreach ($repositories as $repo) {
    if (!MONITOR_PLUGIN_CATALOG_isDiscoverable($repo)) {
        continue;
    }

    $normalized = MONITOR_PLUGIN_ICONS_normalize(isset($repo['name']) ? $repo['name'] : '');
    if ($normalized === '' || isset($installedNames[$normalized])) {
        continue;
    }

    $manifest = null;
    $repoName = isset($repo['name']) ? (string) $repo['name'] : '';
    $icons[$normalized] = MONITOR_PLUGIN_ICONS_resolveRepository($owner, $repo, $refresh, $manifest);

    /*
     * Discovery is release-oriented. The repository default branch may target
     * a newer runtime than the site even though an older stable release is
     * still installable. Resolve stable releases first, then use master only
     * as a metadata fallback when no compatible release metadata exists.
     */
    $latest = MONITOR_PLUGIN_CATALOG_latestVersion($owner, $repoName, $refresh);
    $compatible = MONITOR_PLUGIN_CATALOG_latestCompatibleRelease(
        $owner,
        $repoName,
        defined('VERSION') ? (string) VERSION : '',
        PHP_VERSION,
        $refresh
    );

    if (is_array($compatible)) {
        $requirements = isset($compatible['requirements'])
            && is_array($compatible['requirements'])
            ? $compatible['requirements'] : array();
        $compatibleGeeklog = isset($requirements['geeklog'])
            ? (string) $requirements['geeklog'] : '';
        $compatiblePhp = isset($requirements['php'])
            ? (string) $requirements['php'] : '';

        $itemMetadata = array(
            'id' => is_array($manifest) && isset($manifest['id'])
                ? (string) $manifest['id'] : '',
            'name' => is_array($manifest) && isset($manifest['name'])
                ? (string) $manifest['name'] : '',
            'requires' => array(
                'geeklog' => $compatibleGeeklog,
                'php' => $compatiblePhp
            ),
            'compatibility' => array(
                'state' => 'compatible',
                'geeklog' => $compatibleGeeklog !== ''
                    ? MONITOR_PLUGIN_ICONS_requirementState(
                        defined('VERSION') ? (string) VERSION : '',
                        $compatibleGeeklog
                    ) : 'not_declared',
                'php' => $compatiblePhp !== ''
                    ? MONITOR_PLUGIN_ICONS_requirementState(PHP_VERSION, $compatiblePhp)
                    : 'not_declared',
                'geeklog_current' => defined('VERSION') ? (string) VERSION : '',
                'php_current' => PHP_VERSION
            ),
            'requirements_declared' => ($compatibleGeeklog !== '' || $compatiblePhp !== ''),
            'manifest_available' => is_array($manifest),
            'source' => isset($compatible['source']) ? (string) $compatible['source'] : 'compatible_release',
            'latest_compatible_version' => isset($compatible['tag'])
                ? (string) $compatible['tag'] : '',
            'latest_compatible_url' => isset($compatible['url'])
                ? (string) $compatible['url'] : ''
        );

        if (is_array($latest)) {
            $itemMetadata['latest_overall_version'] = isset($latest['tag'])
                ? (string) $latest['tag'] : '';
            $itemMetadata['latest_overall_url'] = isset($latest['url'])
                ? (string) $latest['url'] : '';

            if (!empty($itemMetadata['latest_overall_version'])
                    && $itemMetadata['latest_overall_version']
                        !== $itemMetadata['latest_compatible_version']) {
                $overallRelease = null;
                foreach (MONITOR_PLUGIN_CATALOG_releases($owner, $repoName, $refresh) as $release) {
                    if (is_array($release)
                            && isset($release['tag_name'])
                            && (string) $release['tag_name'] === $itemMetadata['latest_overall_version']) {
                        $overallRelease = $release;
                        break;
                    }
                }
                if (is_array($overallRelease)) {
                    $overallReq = MONITOR_PLUGIN_CATALOG_releaseRequirements(
                        $owner,
                        $repoName,
                        $overallRelease,
                        $refresh
                    );
                    $itemMetadata['latest_overall_requires'] = array(
                        'geeklog' => isset($overallReq['geeklog'])
                            ? (string) $overallReq['geeklog'] : '',
                        'php' => isset($overallReq['php'])
                            ? (string) $overallReq['php'] : ''
                    );
                }
            }
        }

        $metadata[$normalized] = $itemMetadata;
    } elseif (is_array($manifest)) {
        $metadata[$normalized] = MONITOR_PLUGIN_ICONS_metadata(
            $manifest,
            'remote_plugin_json'
        );
        if (is_array($latest)) {
            $metadata[$normalized]['latest_overall_version'] = isset($latest['tag'])
                ? (string) $latest['tag'] : '';
            $metadata[$normalized]['latest_overall_url'] = isset($latest['url'])
                ? (string) $latest['url'] : '';
        }
    } else {
        $metadata[$normalized] = array(
            'id' => '',
            'name' => '',
            'requires' => array('geeklog' => '', 'php' => ''),
            'compatibility' => array(
                'state' => 'metadata_unavailable',
                'geeklog' => 'unknown',
                'php' => 'unknown',
                'geeklog_current' => defined('VERSION') ? (string) VERSION : '',
                'php_current' => PHP_VERSION
            ),
            'requirements_declared' => false,
            'manifest_available' => false,
            'source' => 'no_plugin_json'
        );
    }

}

echo json_encode(array(
    'ok' => true,
    'fallback' => MONITOR_PLUGIN_ICONS_fallbackUrl(),
    'icons' => $icons,
    'metadata' => $metadata
));
