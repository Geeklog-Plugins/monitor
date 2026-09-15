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

/**
 * Normalize a version only when it is safe to compare with version_compare().
 *
 * @param mixed $version
 * @return string
 */
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

/**
 * Inspect local plugin files using Geeklog APIs only.
 *
 * PLG_chkVersion() is authoritative when the callback is loaded. Disabled
 * plugins may not have their functions.inc loaded, so PLG_getParams() is used
 * as a safe metadata fallback instead of including plugin code from Monitor.
 *
 * @param string $pluginName
 * @return array
 */
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

/**
 * Resolve the local alignment state between the persisted DB version and code.
 *
 * @param string $installedVersion
 * @param array  $code
 * @return array
 */
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

/**
 * Enrich monitor.get_plugins without duplicating this logic in consumers.
 *
 * Remote update state is calculated from the local code version when known.
 * This keeps "upgrade required" (DB < local code) separate from "update
 * available" (GitHub > local code).
 *
 * @param array $envelope
 * @return array
 */
function MONITOR_PLUGIN_VERSIONS_enrichServiceEnvelope($envelope)
{
    if (!is_array($envelope) || !isset($envelope['data']) || !is_array($envelope['data'])) {
        return $envelope;
    }

    $plugins = isset($envelope['data']['plugins']) && is_array($envelope['data']['plugins'])
        ? $envelope['data']['plugins'] : array();
    $upgrades = 0;
    $updates = 0;

    foreach ($plugins as $index => $plugin) {
        if (!is_array($plugin)) {
            continue;
        }

        $name = isset($plugin['name']) ? (string) $plugin['name'] : '';
        $installed = isset($plugin['installed_version'])
            ? (string) $plugin['installed_version'] : '';
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
            $plugin['version_state'] = ($referenceVersion !== '')
                ? MONITOR_PLUGIN_CATALOG_versionState($referenceVersion, $remoteVersion)
                : 'unknown';
        }

        if (isset($plugin['version_state']) && $plugin['version_state'] === 'update') {
            $updates++;
        }

        $plugins[$index] = $plugin;
    }

    $envelope['data']['plugins'] = $plugins;
    if (!isset($envelope['data']['summary']) || !is_array($envelope['data']['summary'])) {
        $envelope['data']['summary'] = array();
    }
    $envelope['data']['summary']['upgrades_required'] = $upgrades;
    $envelope['data']['summary']['updates_available'] = $updates;

    return $envelope;
}
