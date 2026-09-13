<?php

/**
 * Small compatibility helpers shared by Monitor 1.4.0 components.
 *
 * Keep this file compatible with PHP 5.6 through PHP 8.1.
 *
 * @package Monitor
 */

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'monitorcompat.php') !== false) {
    die('This file can not be used on its own.');
}

/**
 * Return a readable local timestamp without relying directly on deprecated
 * strftime() on PHP 8.1.
 *
 * @return string
 */
function MONITOR_timestamp()
{
    if (is_callable('COM_strftime')) {
        return COM_strftime('%c');
    }

    return date('Y-m-d H:i:s');
}
