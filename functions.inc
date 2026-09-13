<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | Geeklog Plugin API methods and common Monitor runtime helpers.            |
// +---------------------------------------------------------------------------+

/**
 * @package Monitor
 */

if (isset($_SERVER['PHP_SELF']) &&
        strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file can not be used on its own.');
}

$plugin_path = $_CONF['path'] . 'plugins/monitor/';
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}

require_once $_CONF['path_system'] . 'classes/config.class.php';
$c = config::get_instance();
$_MONITOR_CONF = $c->get_config('monitor');

$_TABLES['monitor_ban'] = $_DB_table_prefix . 'monitor_ban';

/*
 * Keep legacy Monitor ban data readable during the 1.4.0 transition.
 * Monitor no longer creates automatic permanent bans itself.
 */
MONITOR_banCheck();

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API                                                        |
// +---------------------------------------------------------------------------+

function plugin_cclabel_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    if (!SEC_hasRights('monitor.admin')) {
        return array();
    }

    return array(
        $LANG_MONITOR_1['plugin_name'],
        $_CONF['site_admin_url'] . '/plugins/monitor/index.php',
        plugin_geticon_monitor()
    );
}

function plugin_getadminoption_monitor()
{
    global $_CONF, $LANG_MONITOR_1;

    if (!SEC_hasRights('monitor.admin')) {
        return null;
    }

    return array(
        $LANG_MONITOR_1['plugin_name'],
        $_CONF['site_admin_url'] . '/plugins/monitor/index.php',
        0
    );
}

function plugin_geticon_monitor()
{
    global $_CONF;

    return $_CONF['site_admin_url'] . '/plugins/monitor/images/monitor.png';
}

function plugin_chkVersion_monitor()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/monitor/autoinstall.php';
    $inst_parms = plugin_autoinstall_monitor('monitor');

    return $inst_parms['info']['pi_version'];
}

/**
 * Upgrade Monitor sequentially and only record 1.4.0 after required migrations
 * have completed successfully.
 *
 * @return int|bool
 */
function plugin_upgrade_monitor()
{
    global $_CONF, $_TABLES, $_MONITOR_CONF, $_monitor_DEFAULT;

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

    /* Very old installations may not yet have the legacy security table. */
    if (version_compare($installed_version, '1.1.1', '<')) {
        if (!DB_checkTableExists('monitor_ban')) {
            $sql = "CREATE TABLE {$_TABLES['monitor_ban']} ("
                 . " bantype varchar(40) NOT NULL default '',"
                 . " data varchar(255) NOT NULL default '',"
                 . " created datetime NOT NULL,"
                 . " access int(10) unsigned NOT NULL default 0,"
                 . " KEY monitor_bantype_data (bantype, data(128)),"
                 . " KEY monitor_created (created)"
                 . ") ENGINE=InnoDB";

            if (!DB_query($sql, 1)) {
                return 3003;
            }
        }
    }

    /* Preserve the historical email setting when upgrading old installations. */
    if (version_compare($installed_version, '1.2.1', '<')) {
        $c = config::get_instance();
        if (!isset($_MONITOR_CONF['emails']) || $_MONITOR_CONF['emails'] == '') {
            if (!$c->group_exists('monitor')) {
                $c->add('sg_main', null, 'subgroup', 0, 0, null, 0, true, 'monitor');
                $c->add('fs_main', null, 'fieldset', 0, 0, null, 0, true, 'monitor');
            }
            $c->add(
                'emails',
                $_CONF['site_mail'],
                'text',
                0,
                0,
                0,
                10,
                true,
                'monitor'
            );
        }
    }

    /* Preserve the historical repository setting for existing installations. */
    if (version_compare($installed_version, '1.3.0', '<')) {
        $c = config::get_instance();
        if (!isset($_MONITOR_CONF['repository']) || $_MONITOR_CONF['repository'] == '') {
            $c->add(
                'repository',
                $_monitor_DEFAULT['repository'],
                'text',
                0,
                0,
                0,
                20,
                true,
                'monitor'
            );
        }
    }

    /*
     * 1.4.0 schema migration. It is deliberately idempotent so a failed or
     * interrupted upgrade can be retried safely.
     */
    if (version_compare($installed_version, '1.4.0', '<')) {
        if (!MONITOR_upgradeSecurityTable140()) {
            COM_errorLog(
                'MONITOR - Upgrade to 1.4.0 stopped before version update: '
                . 'monitor_ban migration failed.'
            );
            return 3003;
        }
    }

    $inst_parms = plugin_autoinstall_monitor('monitor');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];
    $safeCodeVersion = MONITOR_dbEscape($code_version);
    $safeGlVersion = MONITOR_dbEscape($pi_gl_version);

    $updated = DB_query(
        "UPDATE {$_TABLES['plugins']} "
        . "SET pi_version = '{$safeCodeVersion}', "
        . "pi_gl_version = '{$safeGlVersion}' "
        . "WHERE pi_name = 'monitor'",
        1
    );

    if (!$updated) {
        return 3003;
    }

    COM_errorLog(
        'MONITOR - Upgrade completed from ' . $installed_version
        . ' to ' . $code_version . '.'
    );

    return true;
}

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
// | Database / upgrade helpers                                                |
// +---------------------------------------------------------------------------+

function MONITOR_dbEscape($value)
{
    $value = (string) $value;

    if (function_exists('DB_escapeString')) {
        return DB_escapeString($value);
    }

    return addslashes($value);
}

/**
 * Migrate the legacy Monitor security table for 1.4.0.
 *
 * The table is retained for compatibility but is no longer intended to grow
 * into a second full Ban engine.
 *
 * @return bool
 */
function MONITOR_upgradeSecurityTable140()
{
    global $_TABLES;

    if (!DB_checkTableExists('monitor_ban')) {
        $sql = "CREATE TABLE {$_TABLES['monitor_ban']} ("
             . " bantype varchar(40) NOT NULL default '',"
             . " data varchar(255) NOT NULL default '',"
             . " created datetime NOT NULL,"
             . " access int(10) unsigned NOT NULL default 0,"
             . " KEY monitor_bantype_data (bantype, data(128)),"
             . " KEY monitor_created (created)"
             . ") ENGINE=InnoDB";

        return (bool) DB_query($sql, 1);
    }

    $table = $_TABLES['monitor_ban'];
    $safeTableName = MONITOR_dbEscape($table);

    /* Avoid rebuilding an already migrated InnoDB table on every retry. */
    $statusResult = DB_query(
        "SHOW TABLE STATUS WHERE Name = '{$safeTableName}'",
        1
    );
    if (!$statusResult) {
        return false;
    }

    $statusRow = DB_fetchArray($statusResult);
    $engine = is_array($statusRow) && isset($statusRow['Engine'])
        ? $statusRow['Engine']
        : '';

    if (strcasecmp($engine, 'InnoDB') !== 0) {
        if (!DB_query("ALTER TABLE {$table} ENGINE=InnoDB", 1)) {
            return false;
        }
    }

    /* Normalize the counter column used by fresh 1.4.0 installations. */
    if (!DB_query(
        "ALTER TABLE {$table} MODIFY access int(10) unsigned NOT NULL default 0",
        1
    )) {
        return false;
    }

    $existingIndexes = array();
    $result = DB_query("SHOW INDEX FROM {$table}", 1);
    if (!$result) {
        return false;
    }

    while ($row = DB_fetchArray($result)) {
        if (isset($row['Key_name'])) {
            $existingIndexes[$row['Key_name']] = true;
        } elseif (isset($row['key_name'])) {
            $existingIndexes[$row['key_name']] = true;
        }
    }

    if (!isset($existingIndexes['monitor_bantype_data'])) {
        if (!DB_query(
            "ALTER TABLE {$table} "
            . "ADD INDEX monitor_bantype_data (bantype, data(128))",
            1
        )) {
            return false;
        }
    }

    if (!isset($existingIndexes['monitor_created'])) {
        if (!DB_query(
            "ALTER TABLE {$table} ADD INDEX monitor_created (created)",
            1
        )) {
            return false;
        }
    }

    return true;
}

// +---------------------------------------------------------------------------+
// | Log helpers                                                               |
// +---------------------------------------------------------------------------+

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
    if ($size > $readBytes && fseek($handle, -$readBytes, SEEK_END) !== 0) {
        fclose($handle);
        return '';
    }

    $data = fread($handle, $readBytes);
    fclose($handle);

    return ($data === false) ? '' : $data;
}

/**
 * Email a bounded, non-destructive snapshot of selected logs.
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

        $message .= '<p><b>Log File: '
                 . htmlspecialchars($log, ENT_QUOTES, 'UTF-8')
                 . '</b></p><pre>'
                 . htmlspecialchars($contents, ENT_QUOTES, 'UTF-8')
                 . '</pre><hr>';
    }

    if ($message === '') {
        return;
    }

    foreach (explode(',', $_MONITOR_CONF['emails']) as $address) {
        $contact = trim($address);
        if ($contact === '') {
            continue;
        }

        COM_mail(
            $contact,
            $_CONF['site_name'] . ' | Log files',
            $message,
            '',
            true
        );
    }
}

// +---------------------------------------------------------------------------+
// | Scheduled diagnostics                                                     |
// +---------------------------------------------------------------------------+

function plugin_runScheduledTask_monitor()
{
    global $_TABLES, $_CONF;

    /*
     * Geeklog does not rotate its own .log files. Monitor archives them once
     * per calendar day and retains daily copies for the configured period.
     * A failed archive copy never truncates the active log.
     */
    require_once $_CONF['path'] . 'plugins/monitor/lib/MonitorLogRotation.php';
    $rotation = MONITOR_LOG_rotateDaily();
    if (!empty($rotation['failed'])) {
        COM_errorLog(
            'MONITOR - Daily log rotation could not archive '
            . (int) $rotation['failed'] . ' log file(s). Active logs were preserved.'
        );
    }

    /* Keep transitional observations bounded. */
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

        if ($result) {
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
    }

    /* Database checks are diagnostic only; no automatic repair is attempted. */
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
// | Transitional security observations                                       |
// +---------------------------------------------------------------------------+

/**
 * Return the directly connected client IP.
 *
 * Proxy-supplied headers are intentionally not trusted unless a future explicit
 * trusted-proxy feature validates the immediate proxy first.
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
    $row = $result ? DB_fetchArray($result) : false;

    if (is_array($row) && isset($row['access'])) {
        DB_query(
            "UPDATE {$_TABLES['monitor_ban']} "
            . "SET access = access + 1, created = NOW() "
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
 * Preserve only explicit legacy `banned` records during the transition.
 * Detection signals are observations; enforcement belongs to Ban when present.
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
    $row = $result ? DB_fetchArray($result) : false;

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
 * Backward-compatible wrapper for older Monitor callers.
 */
function MONITOR_checkHtaccess($remoteAddress)
{
    if (filter_var($remoteAddress, FILTER_VALIDATE_IP) === false) {
        return;
    }

    MONITOR_denyRequest($remoteAddress);
}
