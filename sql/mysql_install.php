<?php

// +---------------------------------------------------------------------------+
// | Monitor Plugin 1.4.0                                                      |
// +---------------------------------------------------------------------------+
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+

/**
 * Fresh-install schema.
 *
 * `monitor_ban` is retained during the 1.4.0 transition for legacy ban data
 * and short-lived security observations. Monitor no longer treats this table as
 * a full replacement for the dedicated Ban plugin.
 *
 * The data prefix is deliberately limited in the composite index so the schema
 * remains compatible with older MySQL/InnoDB index-size limits when utf8mb4 is
 * used by Geeklog.
 */

$_SQL[] = "
CREATE TABLE " . $_TABLES['monitor_ban'] . " (
    bantype varchar(40) NOT NULL default '',
    data varchar(255) NOT NULL default '',
    created datetime NOT NULL,
    access int(10) unsigned NOT NULL default 0,
    KEY monitor_bantype_data (bantype, data(128)),
    KEY monitor_created (created)
) ENGINE=InnoDB
";
