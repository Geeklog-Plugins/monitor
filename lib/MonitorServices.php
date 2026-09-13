<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorServices.php                                                       |
// |                                                                           |
// | Structured read-only data providers used by Geeklog service callbacks.   |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorservices.php') !== false) {
    die('This file can not be used on its own.');
}

function MONITOR_SERVICE_envelope($service, $data)
{
    return array(
        'provider' => 'monitor',
        'schema_version' => 1,
        'service' => (string) $service,
        'generated_at' => time(),
        'data' => is_array($data) ? $data : array()
    );
}

function MONITOR_SERVICE_safeHealthChecks($checks)
{
    $safe = array();

    foreach ($checks as $check) {
        $id = isset($check['id']) ? (string) $check['id'] : '';
        $value = isset($check['value']) ? (string) $check['value'] : '';

        /* Never expose configured filesystem paths through the service layer. */
        if (strpos($id, 'path.') === 0) {
            $status = isset($check['status']) ? $check['status'] : 'info';
            $value = ($status === 'ok') ? 'available' : 'needs attention';
        }

        $safe[] = array(
            'id' => $id,
            'label' => isset($check['label']) ? (string) $check['label'] : '',
            'status' => isset($check['status']) ? (string) $check['status'] : 'info',
            'value' => $value,
            'recommendation' => isset($check['recommendation'])
                ? (string) $check['recommendation'] : ''
        );
    }

    return $safe;
}

function MONITOR_SERVICE_status()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorHealth.php';

    $checks = MONITOR_HEALTH_collect();
    $summary = MONITOR_HEALTH_summary($checks);
    $overall = 'ok';
    if (!empty($summary['error'])) {
        $overall = 'error';
    } elseif (!empty($summary['warning'])) {
        $overall = 'warning';
    } elseif (!empty($summary['info'])) {
        $overall = 'info';
    }

    return MONITOR_SERVICE_envelope('monitor.get_status', array(
        'status' => $overall,
        'summary' => $summary,
        'checks' => MONITOR_SERVICE_safeHealthChecks($checks)
    ));
}

function MONITOR_SERVICE_changes()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorChanges.php';

    $history = MONITOR_CHANGES_readHistory();
    $count = count($history);
    $data = array(
        'snapshots' => $count,
        'available' => ($count >= 2),
        'from' => null,
        'to' => null,
        'changes' => array(),
        'log' => array(
            'available' => false,
            'rotated' => false,
            'truncated' => false,
            'bytes' => 0,
            'signatures' => array()
        )
    );

    if ($count < 2) {
        return MONITOR_SERVICE_envelope('monitor.get_changes', $data);
    }

    $previous = $history[$count - 2];
    $current = $history[$count - 1];
    $data['from'] = isset($previous['timestamp']) ? (int) $previous['timestamp'] : null;
    $data['to'] = isset($current['timestamp']) ? (int) $current['timestamp'] : null;
    $data['changes'] = MONITOR_CHANGES_compare($previous, $current);
    $data['log'] = MONITOR_CHANGES_errorDelta($previous, $current);

    return MONITOR_SERVICE_envelope('monitor.get_changes', $data);
}

function MONITOR_SERVICE_plugins($args)
{
    global $_CONF, $_TABLES;

    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorPluginCatalog.php';

    $includeRemote = !isset($args['include_remote']) || !empty($args['include_remote']);
    $catalog = array('available' => false, 'owner' => '', 'repositories' => array());
    if ($includeRemote) {
        $catalog = MONITOR_PLUGIN_CATALOG_repositories(false);
    }

    $owner = isset($catalog['owner']) ? (string) $catalog['owner'] : '';
    $repositories = isset($catalog['repositories']) && is_array($catalog['repositories'])
        ? $catalog['repositories'] : array();
    $plugins = array();
    $enabled = 0;
    $disabled = 0;
    $updates = 0;

    $result = DB_query(
        "SELECT pi_name, pi_version, pi_enabled, pi_gl_version "
        . "FROM {$_TABLES['plugins']} ORDER BY pi_name",
        1
    );

    if ($result) {
        while ($row = DB_fetchArray($result)) {
            if (empty($row['pi_name'])) {
                continue;
            }

            $name = (string) $row['pi_name'];
            $installed = isset($row['pi_version']) ? (string) $row['pi_version'] : '';
            $isEnabled = !empty($row['pi_enabled']);
            $state = 'not_checked';
            $latest = '';
            $repositoryUrl = '';
            $versionUrl = '';

            if ($isEnabled) {
                $enabled++;
            } else {
                $disabled++;
            }

            if ($includeRemote && !empty($catalog['available'])) {
                $repo = MONITOR_PLUGIN_CATALOG_matchRepository($name, $repositories);
                if (is_array($repo)) {
                    $repositoryUrl = isset($repo['url']) ? (string) $repo['url'] : '';
                    $remote = MONITOR_PLUGIN_CATALOG_latestVersion(
                        $owner,
                        isset($repo['name']) ? $repo['name'] : '',
                        false
                    );
                    if (is_array($remote)) {
                        $latest = isset($remote['tag']) ? (string) $remote['tag'] : '';
                        $versionUrl = isset($remote['url']) ? (string) $remote['url'] : '';
                        $state = MONITOR_PLUGIN_CATALOG_versionState(
                            $installed,
                            isset($remote['version']) ? $remote['version'] : ''
                        );
                        if ($state === 'update') {
                            $updates++;
                        }
                    } else {
                        $state = 'no_version';
                    }
                } else {
                    $state = 'no_repository';
                }
            }

            $plugins[] = array(
                'name' => $name,
                'installed_version' => $installed,
                'enabled' => $isEnabled,
                'geeklog_requirement' => isset($row['pi_gl_version'])
                    ? (string) $row['pi_gl_version'] : '',
                'version_state' => $state,
                'latest_version' => $latest,
                'repository_url' => $repositoryUrl,
                'version_url' => $versionUrl
            );
        }
    }

    return MONITOR_SERVICE_envelope('monitor.get_plugins', array(
        'summary' => array(
            'installed' => count($plugins),
            'enabled' => $enabled,
            'disabled' => $disabled,
            'updates_available' => $updates
        ),
        'remote_checked' => $includeRemote,
        'remote_available' => $includeRemote && !empty($catalog['available']),
        'repository_owner' => $owner,
        'plugins' => $plugins
    ));
}

function MONITOR_SERVICE_logSummary($args)
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorLogRotation.php';

    $requestedDate = isset($args['date']) ? trim((string) $args['date']) : '';
    if ($requestedDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
        $requestedDate = '';
    }

    $archives = MONITOR_LOG_listArchives();
    $date = $requestedDate;
    if ($date === '' && !empty($archives)) {
        $date = isset($archives[0]['date']) ? (string) $archives[0]['date'] : '';
    }

    $items = array();
    $patterns = array();
    $totalBytes = 0;
    $totalLines = 0;
    $totalIssues = 0;

    foreach ($archives as $archive) {
        if ($date === '' || empty($archive['date']) || $archive['date'] !== $date) {
            continue;
        }

        $path = MONITOR_LOG_archivePath($archive['file']);
        if ($path === '') {
            continue;
        }

        $stats = MONITOR_LOG_archiveStats($path);
        $size = isset($archive['size']) && is_numeric($archive['size'])
            ? (int) $archive['size'] : 0;
        $totalBytes += $size;
        $totalLines += (int) $stats['lines'];
        $totalIssues += (int) $stats['issue_lines'];

        if (strcasecmp($archive['log'], 'error.log') === 0) {
            foreach ($stats['patterns'] as $pattern => $count) {
                if (!isset($patterns[$pattern])) {
                    $patterns[$pattern] = 0;
                }
                $patterns[$pattern] += (int) $count;
            }
        }

        $items[] = array(
            'log' => (string) $archive['log'],
            'archive' => (string) $archive['file'],
            'size_bytes' => $size,
            'lines_scanned' => (int) $stats['lines'],
            'issue_lines' => (int) $stats['issue_lines'],
            'partial' => !empty($stats['truncated']),
            'view_url' => $_CONF['site_admin_url'] . '/plugins/monitor/log-archives.php?file='
                . rawurlencode($archive['file'])
        );
    }

    arsort($patterns);
    $patterns = array_slice($patterns, 0, 10, true);
    $top = array();
    foreach ($patterns as $pattern => $count) {
        $top[] = array('pattern' => (string) $pattern, 'count' => (int) $count);
    }

    return MONITOR_SERVICE_envelope('monitor.get_log_summary', array(
        'date' => $date,
        'available' => !empty($items),
        'totals' => array(
            'archives' => count($items),
            'size_bytes' => $totalBytes,
            'lines_scanned' => $totalLines,
            'issue_lines' => $totalIssues
        ),
        'top_error_patterns' => $top,
        'logs' => $items,
        'archives_url' => $_CONF['site_admin_url'] . '/plugins/monitor/log-archives.php'
    ));
}

function MONITOR_SERVICE_logArchives($args)
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorLogRotation.php';

    $requestedDate = isset($args['date']) ? trim((string) $args['date']) : '';
    if ($requestedDate !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
        $requestedDate = '';
    }

    $limit = isset($args['limit']) ? (int) $args['limit'] : 90;
    if ($limit < 1) {
        $limit = 1;
    } elseif ($limit > 365) {
        $limit = 365;
    }

    $result = array();
    foreach (MONITOR_LOG_listArchives() as $archive) {
        if ($requestedDate !== '' && $archive['date'] !== $requestedDate) {
            continue;
        }
        $result[] = array(
            'date' => (string) $archive['date'],
            'log' => (string) $archive['log'],
            'archive' => (string) $archive['file'],
            'size_bytes' => isset($archive['size']) && is_numeric($archive['size'])
                ? (int) $archive['size'] : null,
            'view_url' => $_CONF['site_admin_url'] . '/plugins/monitor/log-archives.php?file='
                . rawurlencode($archive['file'])
        );
        if (count($result) >= $limit) {
            break;
        }
    }

    return MONITOR_SERVICE_envelope('monitor.get_log_archives', array(
        'retention_days' => MONITOR_LOG_retentionDays(),
        'count' => count($result),
        'archives' => $result,
        'archives_url' => $_CONF['site_admin_url'] . '/plugins/monitor/log-archives.php'
    ));
}

function MONITOR_SERVICE_configurationAudit()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorConfigAudit.php';

    $audit = MONITOR_CONFIG_AUDIT_collect();
    $rows = array();
    foreach ($audit['rows'] as $row) {
        $rows[] = array(
            'key' => isset($row['key']) ? (string) $row['key'] : '',
            'status' => isset($row['status']) ? (string) $row['status'] : '',
            'level' => isset($row['level']) ? (string) $row['level'] : 'info',
            'sensitive' => !empty($row['sensitive']),
            'path_checked' => !empty($row['path']['checked']),
            'path_exists' => isset($row['path']['exists']) ? $row['path']['exists'] : null
        );
    }

    $summary = isset($audit['summary']) && is_array($audit['summary'])
        ? $audit['summary'] : array();

    return MONITOR_SERVICE_envelope('monitor.get_configuration_audit', array(
        'consistent' => empty($summary['issues']) && empty($summary['review']),
        'siteconfig_readable' => !empty($audit['siteconfig_readable']),
        'summary' => $summary,
        'items' => $rows
    ));
}
