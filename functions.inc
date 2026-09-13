<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin                                                            |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | Geeklog Plugin API methods and common Monitor helpers.                    |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file can not be used on its own.');
}

/**
 * Language file include
 */
$plugin_path = $_CONF['path'] . 'plugins/monitor/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

/**
 * Load Monitor configuration
 */
require_once $_CONF['path_system'] . 'classes/config.class.php';
$c = config::get_instance();
$_MONITOR_CONF = $c->get_config('monitor');

$_TABLES['monitor_ban'] = $_DB_table_prefix . 'monitor_ban';

/*
 * Keep legacy Monitor ban data readable during the 1.4.0 transition.
 * New automatic permanent bans are deliberately not created here.
 */
MONITOR_banCheck();

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementation                                         |
// +---------------------------------------------------------------------------+

/**
 * Return plugin entry for Command and Control.
 *
 * @return array
 */
function plugin_cclabel_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    $retval = array();
    if (SEC_hasRights('monitor.admin')) {
        $retval = array(
            $LANG_MONITOR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/monitor/index.php',
            plugin_geticon_monitor()
        );
    }

    return $retval;
}

/**
 * Return plugin entry for the Admins Only block.
 *
 * @return array|null
 */
function plugin_getadminoption_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    if (SEC_hasRights('monitor.admin')) {
        return array(
            $LANG_MONITOR_1['plugin_name'],
            $_CONF['site_admin_url'] . '/plugins/monitor/index.php',
            0
        );
    }

    return null;
}

/**
 * Return the URL of the plugin icon.
 *
 * @return string
 */
function plugin_geticon_monitor()
{
    global $_CONF;

    return $_CONF['site_admin_url'] . '/plugins/monitor/images/monitor.png';
}

/**
 * Return the current plugin code version.
 *
 * @return string
 */
function plugin_chkVersion_monitor()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/autoinstall.php';
    $inst_parms = plugin_autoinstall_monitor('monitor');

    return $inst_parms['info']['pi_version'];
}

/**
 * Update the plugin.
 *
 * The upgrade path remains compatible with older installations while removing
 * the historical third-party upgrade notification.
 *
 * @return int|bool
 */
function plugin_upgrade_monitor()
{
    global $_CONF, $_TABLES, $_MONITOR_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/autoinstall.php';
    require_once $_CONF['path'] . 'plugins/monitor/install_defaults.php';

    $installed_version = DB_getItem(
        $_TABLES['plugins'],
        'pi_version',
        "pi_name = 'monitor'"
    );
    $code_version = plugin_chkVersion_monitor();

    if ($installed_version == $code_version) {
        return true;
    }

    if (!plugin_compatible_with_this_version_monitor('monitor')) {
        return 3002;
    }

    $inst_parms = plugin_autoinstall_monitor('monitor');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    switch ($installed_version) {
        case '1.0':
        case '1.1.0':
            if (!DB_checkTableExists('monitor_ban')) {
                $sql = "CREATE TABLE " . $_TABLES['monitor_ban'] . " ("
                     . " bantype varchar(40) NOT NULL default '',"
                     . " data varchar(255) NOT NULL default '',"
                     . " created datetime NOT NULL,"
                     . " access int(3) NOT NULL default '0'"
                     . ") ENGINE=MyISAM";
                DB_query($sql);
            }
            // Fall through intentionally for sequential legacy upgrades.

        case '1.1.1':
        case '1.2.0':
            $c = config::get_instance();
            if (!isset($_MONITOR_CONF['emails']) || $_MONITOR_CONF['emails'] == '') {
                if (!$c->group_exists('monitor')) {
                    $c->add('sg_main', null, 'subgroup', 0, 0, null, 0, true, 'monitor');
                    $c->add('fs_main', null, 'fieldset', 0, 0, null, 0, true, 'monitor');
                }
                $c->add('emails', $_CONF['site_mail'],
                    'text', 0, 0, 0, 10, true, 'monitor');
            }
            // Fall through intentionally.

        case '1.2.1':
            $c = config::get_instance();
            if (!isset($_MONITOR_CONF['repository']) || $_MONITOR_CONF['repository'] == '') {
                $c->add('repository', $_monitor_DEFAULT['repository'],
                    'text', 0, 0, 0, 20, true, 'monitor');
            }
            // Fall through intentionally.

        case '1.3.0':
        case '1.3.1':
        default:
            $safeCodeVersion = MONITOR_dbEscape($code_version);
            $safeGlVersion = MONITOR_dbEscape($pi_gl_version);
            DB_query(
                "UPDATE {$_TABLES['plugins']} "
                . "SET pi_version = '{$safeCodeVersion}', "
                . "pi_gl_version = '{$safeGlVersion}' "
                . "WHERE pi_name = 'monitor'"
            );
            break;
    }

    return true;
}

/**
 * Automatic uninstall definition.
 *
 * @return array
 */
function plugin_autouninstall_monitor()
{
    return array(
        'tables' => array('monitor_ban'),
        'groups' => array('Monitor Admin'),
        'features' => array('monitor.admin'),
        'php_blocks' => array(),
        'vars' => array()
    );
}

// +---------------------------------------------------------------------------+
// | General helpers                                                           |
// +---------------------------------------------------------------------------+

/**
 * Escape a string for Geeklog DB queries while keeping compatibility with
 * supported legacy Geeklog versions.
 *
 * @param string $value
 * @return string
 */
function MONITOR_dbEscape($value)
{
    $value = (string) $value;

    if (function_exists('DB_escapeString')) {
        return DB_escapeString($value);
    }

    return addslashes($value);
}

/**
 * Return a formatted local timestamp without relying on deprecated strftime().
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

/**
 * Read the tail of a file without loading an arbitrarily large log into memory.
 *
 * @param string $path
 * @param int    $maxBytes
 * @return string
 */
function MONITOR_readTail($path, $maxBytes)
{
    $maxBytes = max(1024, (int) $maxBytes);

    if (!is_file($path) || !is_readable($path)) {
        return '';
    }

    $size = filesize($path);
    if ($size === false || $size <= 0) {
        return '';
    }

    $handle = fopen($path, 'rb');
    if ($handle === false) {
        return '';
    }

    $readBytes = min($size, $maxBytes);
    if ($size > $readBytes) {
        fseek($handle, -$readBytes, SEEK_END);
    }

    $data = fread($handle, $readBytes);
    fclose($handle);

    return ($data === false) ? '' : $data;
}

/**
 * Email a bounded snapshot of selected logs.
 *
 * This function is intentionally non-destructive: sending a log snapshot no
 * longer deletes or truncates the source log.
 *
 * @param array $logs
 */
function MONITOR_logEmail($logs)
{
    global $_CONF, $_MONITOR_CONF;

    if (!is_array($logs) || empty($_MONITOR_CONF['emails'])) {
        return;
    }

    $message = '';
    $maxBytesPerLog = 65536;

    foreach ($logs as $log) {
        $log = basename((string) $log);
        if ($log === '') {
            continue;
        }

        $path = $_CONF['path_log'] . $log;
        if (!is_file($path)) {
            continue;
        }

        $contents = MONITOR_readTail($path, $maxBytesPerLog);
        if ($contents === '') {
            continue;
        }

        COM_errorLog('MONITOR - Reading log snapshot: ' . $log);
        $message .= '<p><b>Log File: '
                 . htmlspecialchars($log, ENT_QUOTES, 'UTF-8')
                 . '</b></p><pre>'
                 . htmlspecialchars($contents, ENT_QUOTES, 'UTF-8')
                 . '</pre><hr>';
    }

    if ($message === '') {
        return;
    }

    $to = explode(',', $_MONITOR_CONF['emails']);
    foreach ($to as $address) {
        $contact = trim($address);
        if ($contact === '') {
            continue;
        }

        $subject = $_CONF['site_name'] . ' | Log files';
        COM_mail($contact, $subject, $message, '', true);
    }
}

// +---------------------------------------------------------------------------+
// | Image diagnostics                                                         |
// +---------------------------------------------------------------------------+

/**
 * Recursively list images larger than the Monitor threshold.
 *
 * @param string $folder
 * @return string
 */
function MONITOR_recursiveFiles($folder = '')
{
    global $_CONF, $LANG_MONITOR_1;

    set_time_limit(0);

    if ($folder === '') {
        $folder = $_CONF['path_images'];
    }

    if (!is_dir($folder) || !is_readable($folder)) {
        return '';
    }

    $maxwidth = 1600;
    $maxheight = 1600;
    $retval = '<ul>';
    $files = scandir($folder);

    if ($files === false) {
        return '';
    }

    foreach ($files as $file) {
        if ($file === '.' || $file === '..' || strpos($file, '.') === 0) {
            continue;
        }

        $filepath = $folder . '/' . $file;

        if (is_dir($filepath)) {
            $retval .= '<li>' . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . '</li>';
            $retval .= MONITOR_recursiveFiles($filepath);
            continue;
        }

        if (!is_file($filepath) || !is_readable($filepath)) {
            continue;
        }

        $dimensions = @getimagesize($filepath);
        if ($dimensions === false) {
            continue;
        }

        $width = isset($dimensions[0]) ? (int) $dimensions[0] : 0;
        $height = isset($dimensions[1]) ? (int) $dimensions[1] : 0;

        if ($width <= $maxwidth && $height <= $maxheight) {
            continue;
        }

        if (!isset($_SESSION['imgtoresize'])) {
            $_SESSION['imgtoresize'] = 0;
        }
        $_SESSION['imgtoresize']++;

        $size = filesize($filepath);
        $size = ($size === false) ? 0 : (int) ($size / 1024);

        $resizeRequested = isset($_POST['resize'])
            && $_POST['resize'] == $LANG_MONITOR_1['resize_images'];

        if ($resizeRequested) {
            MONITOR_resizeImage($filepath);
        } else {
            $retval .= '<li><span style="color:red;font-weight:bold;">'
                    . htmlspecialchars($file, ENT_QUOTES, 'UTF-8')
                    . ' | ' . $width . 'px/' . $height . 'px | '
                    . $size . ' Ko</span></li>';
        }
    }

    $retval .= '</ul>';

    return $retval;
}

/**
 * Resize a local image using the legacy SimpleImage helper.
 *
 * @param string $img
 * @return bool
 */
function MONITOR_resizeImage($img)
{
    global $_CONF;

    if (!is_file($img) || !is_readable($img) || !is_writable($img)) {
        return false;
    }

    require_once $_CONF['path'] . 'plugins/monitor/class/simpleresize.class.php';

    $maxwidth = 1600;
    $maxheight = 1600;
    $image = new SimpleImage();
    $image->load($img);

    if ($image->getWidth() > $maxwidth) {
        $image->resizeToWidth($maxwidth);
        $image->save($img);
        COM_errorLog('MONITOR - Resizing image ' . $img);
        return true;
    }

    if ($image->getHeight() > $maxheight) {
        $image->resizeToHeight($maxheight);
        $image->save($img);
        COM_errorLog('MONITOR - Resizing image ' . $img);
        return true;
    }

    return false;
}

// +---------------------------------------------------------------------------+
// | Scheduled diagnostics                                                     |
// +---------------------------------------------------------------------------+

/**
 * Run bounded maintenance and diagnostic tasks.
 */
function plugin_runScheduledTask_monitor()
{
    global $_TABLES;

    /*
     * Legacy security observations are retained for 24 hours during the 1.4.0
     * transition. Cleanup belongs here rather than on every frontend request.
     */
    if (DB_checkTableExists('monitor_ban')) {
        DB_query(
            "DELETE FROM {$_TABLES['monitor_ban']} "
            . "WHERE ADDTIME(created, '23:59:59') <= NOW()",
            1
        );

        $result = DB_query(
            "SELECT bantype, COUNT(*) AS total "
            . "FROM {$_TABLES['monitor_ban']} GROUP BY bantype",
            1
        );

        while ($row = DB_fetchArray($result)) {
            if (!isset($row['bantype']) || !isset($row['total'])) {
                continue;
            }
            COM_errorLog(
                'MONITOR - Security observations: '
                . $row['bantype'] . ' = ' . (int) $row['total']
            );
        }
    }

    /*
     * Database table checks are diagnostics only. Keep failures in Geeklog's
     * normal error log and avoid destructive repair operations.
     */
    foreach ($_TABLES as $table) {
        if (!is_string($table) || $table === '') {
            continue;
        }

        $resp = DB_query('CHECK TABLE ' . $table . ' FAST;', 1);
        if (!$resp) {
            continue;
        }

        while ($row = DB_fetchArray($resp)) {
            if (isset($row['Msg_type']) && $row['Msg_type'] == 'Error') {
                COM_errorLog(
                    'MONITOR - ' . $table . ' | '
                    . $row['Msg_type'] . ': '
                    . (isset($row['Msg_text']) ? $row['Msg_text'] : '')
                );
            }
        }
    }
}

// +---------------------------------------------------------------------------+
// | Input helpers                                                             |
// +---------------------------------------------------------------------------+

/**
 * Filter submitted variables.
 *
 * @param array $vars
 * @param array $arr
 */
function MONITOR_filterVars($vars, &$arr)
{
    foreach ($vars as $var => $type) {
        $value = isset($arr[$var]) ? $arr[$var] : '';

        switch ($type) {
            case 'number':
                $arr[$var] = COM_applyFilter($value, true);
                break;

            case 'html':
                $arr[$var] = COM_checkHTML($value, 'monitor.admin');
                break;

            case 'text':
                $arr[$var] = htmlspecialchars(
                    strip_tags(COM_stripslashes($value)),
                    ENT_QUOTES,
                    'UTF-8'
                );
                break;

            case 'alpha':
            default:
                $arr[$var] = COM_applyFilter($value, false);
                break;
        }
    }
}

// +---------------------------------------------------------------------------+
// | Legacy security observation transition                                    |
// +---------------------------------------------------------------------------+

/**
 * Return the directly connected client IP.
 *
 * Proxy supplied headers are intentionally not trusted here. A future trusted
 * proxy feature must explicitly validate the proxy before using forwarded IPs.
 *
 * @return string
 */
function MONITOR_getUserIP()
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? trim($_SERVER['REMOTE_ADDR']) : '';

    if ($ip === '' || filter_var($ip, FILTER_VALIDATE_IP) === false) {
        return '';
    }

    return $ip;
}

/**
 * Record a bounded legacy security observation.
 *
 * @param string $type
 * @param string $ip
 */
function MONITOR_recordSecurityObservation($type, $ip)
{
    global $_TABLES;

    if (!DB_checkTableExists('monitor_ban')) {
        return;
    }

    $allowedTypes = array('profile', 'newuser');
    if (!in_array($type, $allowedTypes, true)) {
        return;
    }

    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
        return;
    }

    $safeType = MONITOR_dbEscape($type);
    $safeIp = MONITOR_dbEscape($ip);

    $result = DB_query(
        "SELECT access FROM {$_TABLES['monitor_ban']} "
        . "WHERE bantype = '{$safeType}' AND data = '{$safeIp}' LIMIT 1",
        1
    );
    $row = DB_fetchArray($result);

    if (is_array($row) && isset($row['access'])) {
        DB_query(
            "UPDATE {$_TABLES['monitor_ban']} SET access = access + 1 "
            . "WHERE bantype = '{$safeType}' AND data = '{$safeIp}'",
            1
        );
    } else {
        DB_query(
            "INSERT INTO {$_TABLES['monitor_ban']} "
            . "(bantype, data, created, access) VALUES "
            . "('{$safeType}', '{$safeIp}', NOW(), 1)",
            1
        );
    }
}

/**
 * Transitional Monitor security check.
 *
 * Existing explicit legacy `banned` records remain enforceable so upgrades do
 * not unexpectedly unblock an address. Monitor no longer creates new permanent
 * bans from CAPTCHA/DokuWiki log scanning or repeated registration/contact
 * requests. Those signals are observations for Monitor; enforcement belongs to
 * the Ban plugin when available.
 */
function MONITOR_banCheck()
{
    global $_TABLES, $_USER;

    $uid = isset($_USER['uid']) ? (int) $_USER['uid'] : 1;
    if ($uid >= 2 || !DB_checkTableExists('monitor_ban')) {
        return;
    }

    $remoteAddress = MONITOR_getUserIP();
    if ($remoteAddress === '') {
        return;
    }

    $safeIp = MONITOR_dbEscape($remoteAddress);
    $result = DB_query(
        "SELECT bantype FROM {$_TABLES['monitor_ban']} "
        . "WHERE data = '{$safeIp}' AND bantype = 'banned' LIMIT 1",
        1
    );
    $row = DB_fetchArray($result);

    if (is_array($row) && isset($row['bantype']) && $row['bantype'] === 'banned') {
        MONITOR_denyRequest($remoteAddress);
    }

    $scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
    $what = isset($_POST['what']) ? $_POST['what'] : '';
    $mode = isset($_POST['mode']) ? $_POST['mode'] : '';

    if (strpos($scriptName, 'profiles.php') !== false && $what === 'contact') {
        MONITOR_recordSecurityObservation('profile', $remoteAddress);
    }

    if (strpos($scriptName, 'users.php') !== false && $mode === 'create') {
        MONITOR_recordSecurityObservation('newuser', $remoteAddress);
    }
}

/**
 * Immediately deny a legacy blocked request.
 *
 * @param string $remoteAddress
 */
function MONITOR_denyRequest($remoteAddress)
{
    if (!headers_sent()) {
        header('HTTP/1.1 403 Forbidden', true, 403);
        header('Content-Type: text/html; charset=UTF-8');
    }

    $safeIp = htmlspecialchars($remoteAddress, ENT_QUOTES, 'UTF-8');

    die(
        '<!doctype html><html><head><meta charset="utf-8">'
        . '<title>Error 403 - Forbidden</title></head><body>'
        . '<h1>Error 403 - Forbidden</h1>'
        . '<p>Access to this site has been denied.</p>'
        . '<p><em>IP Address: ' . $safeIp . '</em></p>'
        . '</body></html>'
    );
}

/**
 * Backward-compatible wrapper retained for callers from older Monitor code.
 *
 * @param string $remoteAddress
 */
function MONITOR_checkHtaccess($remoteAddress)
{
    if (filter_var($remoteAddress, FILTER_VALIDATE_IP) === false) {
        return;
    }

    MONITOR_denyRequest($remoteAddress);
}

?>
