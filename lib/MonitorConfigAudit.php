<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | lib/MonitorConfigAudit.php                                                |
// |                                                                           |
// | Read-only comparison of siteconfig.php keys and Geeklog conf_values.      |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorconfigaudit.php') !== false) {
    die('This file can not be used on its own.');
}

function MONITOR_CONFIG_AUDIT_siteconfigKeys($path)
{
    $keys = array();

    if (!is_file($path) || !is_readable($path)) {
        return $keys;
    }

    $contents = @file_get_contents($path);
    if ($contents === false || $contents === '') {
        return $keys;
    }

    if (preg_match_all(
        '/\$_CONF\s*\[\s*([\'\"])([^\'\"]+)\1\s*\]\s*=/',
        $contents,
        $matches
    )) {
        foreach ($matches[2] as $key) {
            $key = trim($key);
            if ($key !== '') {
                $keys[$key] = true;
            }
        }
    }

    $result = array_keys($keys);
    natcasesort($result);

    return array_values($result);
}

function MONITOR_CONFIG_AUDIT_decode($raw)
{
    if ($raw === 'unset') {
        return array('unset' => true, 'value' => null, 'valid' => true);
    }

    $value = @unserialize($raw);
    $valid = !($value === false && $raw !== serialize(false));

    return array('unset' => false, 'value' => $value, 'valid' => $valid);
}

function MONITOR_CONFIG_AUDIT_isPathKey($key)
{
    if (strpos($key, 'path_') === 0) {
        return true;
    }

    return in_array($key, array('backup_path', 'rdf_file'), true);
}

/**
 * Sensitive values are never printed and never included in candidate SQL.
 *
 * @param string $key
 * @return bool
 */
function MONITOR_CONFIG_AUDIT_isSensitiveKey($key)
{
    $key = strtolower((string) $key);
    $patterns = array(
        'password',
        'passwd',
        'secret',
        'token',
        'private_key',
        'apikey',
        'api_key',
        'auth_key'
    );

    foreach ($patterns as $pattern) {
        if (strpos($key, $pattern) !== false) {
            return true;
        }
    }

    return false;
}

function MONITOR_CONFIG_AUDIT_pathState($key, $value)
{
    if (!MONITOR_CONFIG_AUDIT_isPathKey($key) ||
            !is_string($value) || $value === '') {
        return array('checked' => false, 'exists' => null);
    }

    return array('checked' => true, 'exists' => file_exists($value));
}

function MONITOR_CONFIG_AUDIT_displayValue($value)
{
    if ($value === true) {
        return 'true';
    }
    if ($value === false) {
        return 'false';
    }
    if ($value === null) {
        return 'NULL';
    }
    if (is_array($value)) {
        return print_r($value, true);
    }
    if (is_object($value)) {
        return '[OBJECT]';
    }

    return (string) $value;
}

function MONITOR_CONFIG_AUDIT_collect()
{
    global $_CONF, $_TABLES;

    $siteconfigPath = isset($_CONF['path_html'])
        ? rtrim($_CONF['path_html'], '/\\') . '/siteconfig.php'
        : '';

    if ($siteconfigPath === '' || !is_file($siteconfigPath)) {
        $fallback = isset($_CONF['path'])
            ? dirname(rtrim($_CONF['path'], '/\\')) . '/public_html/siteconfig.php'
            : '';
        if ($fallback !== '' && is_file($fallback)) {
            $siteconfigPath = $fallback;
        }
    }

    $siteKeys = MONITOR_CONFIG_AUDIT_siteconfigKeys($siteconfigPath);
    $siteKeyMap = array();
    foreach ($siteKeys as $key) {
        $siteKeyMap[$key] = true;
    }

    $dbValues = array();
    $result = DB_query(
        "SELECT name, value, default_value, type, subgroup, tab "
        . "FROM {$_TABLES['conf_values']} WHERE group_name = 'Core'",
        1
    );

    if ($result) {
        while ($row = DB_fetchArray($result)) {
            if (!isset($row['name'])) {
                continue;
            }

            $decoded = MONITOR_CONFIG_AUDIT_decode(
                isset($row['value']) ? $row['value'] : ''
            );

            $dbValues[$row['name']] = array(
                'value' => $decoded['value'],
                'unset' => $decoded['unset'],
                'valid' => $decoded['valid'],
                'type' => isset($row['type']) ? $row['type'] : '',
                'subgroup' => isset($row['subgroup']) ? $row['subgroup'] : '',
                'tab' => isset($row['tab']) ? $row['tab'] : ''
            );
        }
    }

    $fileOnlyNormal = array('path', 'path_system', 'site_enabled', 'default_charset');

    $keys = $siteKeys;
    foreach ($dbValues as $key => $value) {
        if (!isset($siteKeyMap[$key])) {
            $keys[] = $key;
        }
    }
    $keys = array_values(array_unique($keys));
    natcasesort($keys);
    $keys = array_values($keys);

    $rows = array();
    $summary = array(
        'identical' => 0,
        'different' => 0,
        'core_file' => 0,
        'file_only' => 0,
        'database_only' => 0,
        'db_unset' => 0,
        'invalid_paths' => 0,
        'decode_errors' => 0,
        'redacted' => 0
    );

    foreach ($keys as $key) {
        $siteExists = isset($siteKeyMap[$key]);
        $dbExists = isset($dbValues[$key]);
        $siteValue = ($siteExists && array_key_exists($key, $_CONF)) ? $_CONF[$key] : null;
        $dbValue = ($dbExists && !$dbValues[$key]['unset']) ? $dbValues[$key]['value'] : null;
        $sensitive = MONITOR_CONFIG_AUDIT_isSensitiveKey($key);

        if ($sensitive) {
            $summary['redacted']++;
        }

        if ($siteExists) {
            $priority = 'siteconfig.php';
            $effective = $siteValue;
        } elseif ($dbExists) {
            $priority = 'database';
            $effective = $dbValue;
        } else {
            $priority = 'unknown';
            $effective = null;
        }

        if ($dbExists && !$dbValues[$key]['valid']) {
            $status = 'DB DECODE ERROR';
            $summary['decode_errors']++;
        } elseif ($dbExists && $dbValues[$key]['unset']) {
            $status = 'DB = unset';
            $summary['db_unset']++;
        } elseif ($siteExists && $dbExists && $siteValue === $dbValue) {
            $status = 'IDENTICAL';
            $summary['identical']++;
        } elseif ($siteExists && $dbExists) {
            $status = 'DIFFERENT';
            $summary['different']++;
        } elseif ($siteExists && in_array($key, $fileOnlyNormal, true)) {
            $status = 'CORE FILE NORMAL';
            $summary['core_file']++;
        } elseif ($siteExists) {
            $status = 'FILE ONLY';
            $summary['file_only']++;
        } else {
            $status = 'DATABASE ONLY';
            $summary['database_only']++;
        }

        $pathState = MONITOR_CONFIG_AUDIT_pathState($key, $effective);
        if ($pathState['checked'] && !$pathState['exists']) {
            $summary['invalid_paths']++;
        }

        $sql = '';
        if (!$sensitive && $siteExists && $dbExists && !$dbValues[$key]['unset'] &&
                $dbValues[$key]['valid'] && $siteValue !== $dbValue) {
            $canSuggest = true;
            if (MONITOR_CONFIG_AUDIT_isPathKey($key)) {
                $sitePath = MONITOR_CONFIG_AUDIT_pathState($key, $siteValue);
                $canSuggest = $sitePath['checked'] && $sitePath['exists'];
            }

            if ($canSuggest) {
                $safeName = MONITOR_dbEscape($key);
                $safeValue = MONITOR_dbEscape(serialize($siteValue));
                $safeTable = str_replace('`', '``', $_TABLES['conf_values']);
                $sql = "UPDATE `{$safeTable}`\n"
                     . "SET `value` = '{$safeValue}'\n"
                     . "WHERE `name` = '{$safeName}'\n"
                     . "  AND `group_name` = 'Core';";
            }
        }

        $rows[] = array(
            'key' => $key,
            'site_exists' => $siteExists,
            'site_value' => $siteValue,
            'db_exists' => $dbExists,
            'db_value' => $dbValue,
            'priority' => $priority,
            'effective_value' => $effective,
            'status' => $status,
            'path' => $pathState,
            'sql' => $sql,
            'sensitive' => $sensitive
        );
    }

    return array(
        'siteconfig_path' => $siteconfigPath,
        'siteconfig_readable' => ($siteconfigPath !== '' && is_readable($siteconfigPath)),
        'rows' => $rows,
        'summary' => $summary
    );
}
