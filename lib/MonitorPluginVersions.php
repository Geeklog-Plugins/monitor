<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorPluginVersions.php                                                 |
// |                                                                           |
// | Local plugin code/database version diagnostics for read-only services.    |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorpluginversions.php') !== false) {
    die('This file can not be used on its own.');
}

function MONITOR_PLUGIN_VERSIONS_comparable($version)
{
    $version = trim((string) $version);
    if ($version === '') {
        return '';
    }

    if (preg_match('/^[vV](?=[0-9])/', $version)) {
        $version = substr($version, 1);
    }

    if (!preg_match('/^[0-9]+(?:\.[0-9]+){1,3}(?:[-+][0-9A-Za-z.-]+)?$/', $version)) {
        return '';
    }

    return $version;
}

function MONITOR_PLUGIN_VERSIONS_requirementVersion($requirement)
{
    $requirement = trim((string) $requirement);
    if ($requirement === '') {
        return '';
    }

    if (preg_match('/([0-9]+(?:\.[0-9]+){1,3}(?:[-+][0-9A-Za-z.-]+)?)/', $requirement, $match)) {
        return MONITOR_PLUGIN_VERSIONS_comparable($match[1]);
    }

    return '';
}

function MONITOR_PLUGIN_VERSIONS_requirementState($current, $requirement)
{
    $currentVersion = MONITOR_PLUGIN_VERSIONS_requirementVersion($current);
    $requiredVersion = MONITOR_PLUGIN_VERSIONS_requirementVersion($requirement);

    if ($requirement === '' || $requiredVersion === '' || $currentVersion === '') {
        return 'unknown';
    }

    return version_compare($currentVersion, $requiredVersion, '>=')
        ? 'compatible' : 'incompatible';
}

function MONITOR_PLUGIN_VERSIONS_siteGeeklogVersion()
{
    global $_CONF;

    if (defined('VERSION')) {
        return trim((string) constant('VERSION'));
    }

    if (isset($_CONF['version']) && is_scalar($_CONF['version'])) {
        return trim((string) $_CONF['version']);
    }

    return '';
}

function MONITOR_PLUGIN_VERSIONS_repositoryName($url)
{
    $path = parse_url((string) $url, PHP_URL_PATH);
    if (!is_string($path) || $path === '') {
        return '';
    }

    $path = trim($path, '/');
    $segments = explode('/', $path);
    if (count($segments) < 2) {
        return '';
    }

    $name = end($segments);
    $name = preg_replace('/\.git$/i', '', (string) $name);

    return preg_match('/^[A-Za-z0-9_.-]+$/', $name) ? $name : '';
}

function MONITOR_PLUGIN_VERSIONS_localCode($pluginName)
{
    global $_CONF;

    $pluginName = trim((string) $pluginName);
    if ($pluginName === '' || !preg_match('/^[A-Za-z0-9_-]+$/', $pluginName)) {
        return array(
            'version' => '',
            'comparable_version' => '',
            'state' => 'code_missing'
        );
    }

    $root = isset($_CONF['path'])
        ? rtrim((string) $_CONF['path'], '/\\') . DIRECTORY_SEPARATOR . 'plugins'
        : '';
    $directory = $root !== ''
        ? $root . DIRECTORY_SEPARATOR . $pluginName
        : '';
    $functionsFile = $directory !== ''
        ? $directory . DIRECTORY_SEPARATOR . 'functions.inc'
        : '';
    $callback = 'plugin_chkVersion_' . $pluginName;

    if ($directory === '' || !is_dir($directory) || !is_file($functionsFile)) {
        return array(
            'version' => '',
            'comparable_version' => '',
            'state' => 'code_missing'
        );
    }

    $codeVersion = '';
    $sourceState = 'callback_unavailable';

    if (function_exists('PLG_chkVersion')) {
        $value = @PLG_chkVersion($pluginName);
        if (is_scalar($value)) {
            $codeVersion = trim((string) $value);
        }
        if ($codeVersion !== '') {
            $sourceState = 'available';
        }
    } elseif (function_exists($callback)) {
        $value = @$callback();
        if (is_scalar($value)) {
            $codeVersion = trim((string) $value);
        }
        if ($codeVersion !== '') {
            $sourceState = 'available';
        }
    }

    if ($codeVersion === '' && function_exists('PLG_getParams')) {
        $params = @PLG_getParams($pluginName);
        if (is_array($params) && isset($params['info']) && is_array($params['info'])
                && isset($params['info']['pi_version']) && is_scalar($params['info']['pi_version'])) {
            $codeVersion = trim((string) $params['info']['pi_version']);
            if ($codeVersion !== '') {
                $sourceState = 'available_from_metadata';
            }
        }
    }

    if ($codeVersion === '') {
        return array(
            'version' => '',
            'comparable_version' => '',
            'state' => function_exists($callback) ? 'version_unavailable' : $sourceState
        );
    }

    $comparable = MONITOR_PLUGIN_VERSIONS_comparable($codeVersion);

    return array(
        'version' => $codeVersion,
        'comparable_version' => $comparable,
        'state' => $comparable === '' ? 'code_version_invalid' : $sourceState
    );
}

function MONITOR_PLUGIN_VERSIONS_localState($installedVersion, $code)
{
    $installed = MONITOR_PLUGIN_VERSIONS_comparable($installedVersion);
    $codeVersion = isset($code['comparable_version'])
        ? (string) $code['comparable_version'] : '';
    $codeState = isset($code['state']) ? (string) $code['state'] : 'version_unavailable';

    if ($installed === '') {
        return array('state' => 'installed_version_invalid', 'upgrade_required' => false);
    }

    if ($codeVersion === '') {
        return array('state' => $codeState, 'upgrade_required' => false);
    }

    $comparison = version_compare($installed, $codeVersion);
    if ($comparison < 0) {
        return array('state' => 'upgrade_required', 'upgrade_required' => true);
    }
    if ($comparison > 0) {
        return array('state' => 'installed_ahead', 'upgrade_required' => false);
    }

    return array('state' => 'current', 'upgrade_required' => false);
}

function MONITOR_PLUGIN_VERSIONS_enrichServiceEnvelope($envelope)
{
    if (!is_array($envelope) || !isset($envelope['data']) || !is_array($envelope['data'])) {
        return $envelope;
    }

    $plugins = isset($envelope['data']['plugins']) && is_array($envelope['data']['plugins'])
        ? $envelope['data']['plugins'] : array();
    $owner = isset($envelope['data']['repository_owner'])
        ? trim((string) $envelope['data']['repository_owner']) : '';
    $siteGeeklog = MONITOR_PLUGIN_VERSIONS_siteGeeklogVersion();
    $sitePhp = PHP_VERSION;
    $upgrades = 0;
    $updates = 0;
    $coreUpdates = 0;
    $compatibleUpdates = 0;
    $incompatibleUpdates = 0;
    $unknownCompatibilityUpdates = 0;

    foreach ($plugins as $index => $plugin) {
        if (!is_array($plugin)) {
            continue;
        }

        $name = isset($plugin['name']) ? (string) $plugin['name'] : '';
        $installed = isset($plugin['installed_version'])
            ? (string) $plugin['installed_version'] : '';
        $distributionSource = isset($plugin['distribution_source'])
            ? (string) $plugin['distribution_source'] : 'standalone';
        $code = MONITOR_PLUGIN_VERSIONS_localCode($name);
        $local = MONITOR_PLUGIN_VERSIONS_localState($installed, $code);

        $plugin['code_version'] = isset($code['version']) ? (string) $code['version'] : '';
        $plugin['upgrade_required'] = !empty($local['upgrade_required']);
        $plugin['local_version_state'] = isset($local['state'])
            ? (string) $local['state'] : 'version_unavailable';

        if ($plugin['upgrade_required']) {
            $upgrades++;
        }

        $latestTag = isset($plugin['latest_version'])
            ? trim((string) $plugin['latest_version']) : '';
        $remoteVersion = function_exists('MONITOR_PLUGIN_CATALOG_versionFromTag')
            ? MONITOR_PLUGIN_CATALOG_versionFromTag($latestTag) : '';
        $installedComparable = MONITOR_PLUGIN_VERSIONS_comparable($installed);
        $codeComparable = isset($code['comparable_version'])
            ? (string) $code['comparable_version'] : '';
        $referenceVersion = $codeComparable !== '' ? $codeComparable : $installedComparable;

        if ($latestTag !== '' && $remoteVersion !== '') {
            $resolvedState = ($referenceVersion !== '')
                ? MONITOR_PLUGIN_CATALOG_versionState($referenceVersion, $remoteVersion)
                : 'unknown';
            if ($distributionSource === 'core' && $resolvedState === 'update') {
                $resolvedState = 'core_update';
            }
            $plugin['version_state'] = $resolvedState;
        }

        $plugin['remote_requirements'] = array(
            'geeklog_min' => '',
            'php_min' => ''
        );
        $plugin['compatibility'] = array(
            'state' => 'unknown',
            'geeklog' => 'unknown',
            'php' => 'unknown',
            'geeklog_current' => $siteGeeklog,
            'php_current' => $sitePhp
        );

        if (isset($plugin['version_state'])
                && $plugin['version_state'] === 'core_update') {
            $updates++;
            $coreUpdates++;
            $coreBaseline = isset($plugin['core_geeklog_baseline'])
                ? (string) $plugin['core_geeklog_baseline'] : '';
            $plugin['remote_requirements'] = array(
                'geeklog_min' => $coreBaseline,
                'php_min' => ''
            );
            $plugin['compatibility'] = array(
                'state' => 'managed_by_core',
                'geeklog' => MONITOR_PLUGIN_VERSIONS_requirementState(
                    $siteGeeklog,
                    $coreBaseline
                ),
                'php' => 'not_applicable',
                'geeklog_current' => $siteGeeklog,
                'php_current' => $sitePhp
            );
        }

        if (isset($plugin['version_state']) && $plugin['version_state'] === 'update') {
            $updates++;
            $repoName = MONITOR_PLUGIN_VERSIONS_repositoryName(
                isset($plugin['repository_url']) ? $plugin['repository_url'] : ''
            );
            $manifest = null;
            if ($owner !== '' && $repoName !== '' && $latestTag !== ''
                    && function_exists('MONITOR_PLUGIN_CATALOG_manifest')) {
                $manifest = MONITOR_PLUGIN_CATALOG_manifest($owner, $repoName, $latestTag, false);
            }

            /*
             * Some historical tags/releases do not expose plugin.json at the
             * version ref even though the maintained default branch does.
             * Fall back to the repository default branch so consumers receive
             * the current target requirements instead of an empty/old value.
             */
            if (!is_array($manifest) && $owner !== '' && $repoName !== ''
                    && function_exists('MONITOR_PLUGIN_CATALOG_repositories')
                    && function_exists('MONITOR_PLUGIN_CATALOG_matchRepository')) {
                $catalog = MONITOR_PLUGIN_CATALOG_repositories(false);
                $repositories = isset($catalog['repositories']) && is_array($catalog['repositories'])
                    ? $catalog['repositories'] : array();
                $repo = MONITOR_PLUGIN_CATALOG_matchRepository($repoName, $repositories);
                if (is_array($repo) && !empty($repo['default_branch'])) {
                    $manifest = MONITOR_PLUGIN_CATALOG_manifest(
                        $owner,
                        $repoName,
                        (string) $repo['default_branch'],
                        false
                    );
                }
            }

            $geeklogRequired = function_exists('MONITOR_PLUGIN_CATALOG_manifestRequirement')
                ? MONITOR_PLUGIN_CATALOG_manifestRequirement($manifest, 'geeklog') : '';
            $phpRequired = function_exists('MONITOR_PLUGIN_CATALOG_manifestRequirement')
                ? MONITOR_PLUGIN_CATALOG_manifestRequirement($manifest, 'php') : '';
            $geeklogState = MONITOR_PLUGIN_VERSIONS_requirementState($siteGeeklog, $geeklogRequired);
            $phpState = MONITOR_PLUGIN_VERSIONS_requirementState($sitePhp, $phpRequired);
            $overall = 'compatible';

            if ($geeklogState === 'incompatible' || $phpState === 'incompatible') {
                $overall = 'incompatible';
                $incompatibleUpdates++;
            } elseif ($geeklogState === 'unknown' || $phpState === 'unknown') {
                $overall = 'unknown';
                $unknownCompatibilityUpdates++;
            } else {
                $compatibleUpdates++;
            }

            $plugin['remote_requirements'] = array(
                'geeklog_min' => $geeklogRequired,
                'php_min' => $phpRequired
            );
            $plugin['compatibility'] = array(
                'state' => $overall,
                'geeklog' => $geeklogState,
                'php' => $phpState,
                'geeklog_current' => $siteGeeklog,
                'php_current' => $sitePhp
            );
        }

        $plugins[$index] = $plugin;
    }

    $envelope['data']['plugins'] = $plugins;
    if (!isset($envelope['data']['summary']) || !is_array($envelope['data']['summary'])) {
        $envelope['data']['summary'] = array();
    }
    $envelope['data']['summary']['upgrades_required'] = $upgrades;
    $envelope['data']['summary']['updates_available'] = $updates;
    $envelope['data']['summary']['core_updates_available'] = $coreUpdates;
    $envelope['data']['summary']['updates_compatible'] = $compatibleUpdates;
    $envelope['data']['summary']['updates_incompatible'] = $incompatibleUpdates;
    $envelope['data']['summary']['updates_compatibility_unknown'] = $unknownCompatibilityUpdates;
    $envelope['data']['site_runtime'] = array(
        'geeklog_version' => $siteGeeklog,
        'php_version' => $sitePhp
    );

    return $envelope;
}
