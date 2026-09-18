<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | MonitorAdminNavigation.php                                                |
// |                                                                           |
// | Shared navigation for all Monitor administration pages.                   |
// +---------------------------------------------------------------------------+

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitoradminnavigation.php') !== false) {
    die('This file can not be used on its own.');
}

/**
 * Escape navigation output.
 *
 * @param mixed $value
 * @return string
 */
function MONITOR_ADMIN_NAV_h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/**
 * Return the canonical Monitor administration navigation.
 *
 * The two Geeklog-native destinations (logs and configuration) deliberately
 * stay outside Monitor. Configuration remains a POST because Geeklog expects
 * conf_group when opening a plugin's configuration panel.
 *
 * @param string $active overview|changes|security|plugins|logs|log_archives
 * @return string
 */
function MONITOR_ADMIN_NAV_render($active)
{
    global $_CONF, $LANG_MONITOR_1;

    $pluginBase = rtrim($_CONF['site_admin_url'], '/') . '/plugins/monitor/';
    $adminBase = rtrim($_CONF['site_admin_url'], '/') . '/';

    $items = array(
        'overview' => array($LANG_MONITOR_1['home'], $pluginBase . 'index.php?view=overview'),
        'changes' => array($LANG_MONITOR_1['changes'], $pluginBase . 'changes.php'),
        'security' => array($LANG_MONITOR_1['security'], $pluginBase . 'index.php?view=security'),
        'plugins' => array($LANG_MONITOR_1['updates'], $pluginBase . 'index.php?view=plugins'),
        'logs' => array($LANG_MONITOR_1['logs_active_title'], $pluginBase . 'logs.php'),
        'log_archives' => array($LANG_MONITOR_1['log_archive_title'], $pluginBase . 'log-archives.php')
    );

    $baseStyle = 'display:inline-block;padding:7px 11px;border:1px solid #c7ccd1;'
               . 'border-radius:5px;text-decoration:none;';
    $html = '<nav class="monitor-admin-nav" aria-label="Monitor" '
          . 'style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0 0 18px 0">';

    foreach ($items as $key => $item) {
        $style = $baseStyle;
        if ($key === $active) {
            $style .= 'font-weight:bold;background:#eef2f5;';
        }

        $html .= '<a style="' . $style . '" href="' . MONITOR_ADMIN_NAV_h($item[1]) . '">'
              . MONITOR_ADMIN_NAV_h($item[0]) . '</a>';
    }

    $html .= '<form style="display:inline;margin:0" action="'
          . MONITOR_ADMIN_NAV_h($adminBase . 'configuration.php') . '" method="post">'
          . '<input type="hidden" name="conf_group" value="monitor">'
          . '<button type="submit" style="padding:7px 11px">'
          . MONITOR_ADMIN_NAV_h($LANG_MONITOR_1['configuration'])
          . '</button></form>';

    $html .= '</nav>';

    return $html;
}
