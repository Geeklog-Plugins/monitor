<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Monitor Plugin                                                            |
// +---------------------------------------------------------------------------+
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+

/**
 * Fresh-install schema.
 *
 * `monitor_ban` is retained during the 1.4.0 transition for legacy ban data
 * and short-lived security observations. Monitor no longer treats this table as
 * a full replacement for the dedicated Ban plugin.
 */

$_SQL[] = "
CREATE TABLE " . $_TABLES['monitor_ban'] . " (
    bantype varchar(40) NOT NULL default '',
    data varchar(255) NOT NULL default '',
    created datetime NOT NULL,
    access int(10) unsigned NOT NULL default 0,
    KEY monitor_ban_lookup (bantype, data(191)),
    KEY monitor_ban_created (created)
) ENGINE=InnoDB
";

?>
