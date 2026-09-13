# Monitor configuration audit

Monitor 1.4.0 includes a read-only configuration audit for Geeklog multisite and legacy configuration diagnostics.

## Purpose

The audit compares configuration keys declared in the active site's `siteconfig.php` with the `Core` group stored in Geeklog's `conf_values` table.

It reports:

- values present in both locations and identical;
- values present in both locations but different;
- normal Core values that remain file-only;
- additional file-only values;
- database-only values;
- database values stored as `unset`;
- invalid physical paths;
- serialized-value decode errors;
- which source currently has priority;
- the effective runtime value used by Geeklog;
- candidate SQL for selected differences.

## Safety model

The audit is restricted to Geeklog Root administrators.

It is read-only:

- it never writes `siteconfig.php`;
- it never changes `conf_values`;
- candidate SQL is displayed for review only;
- candidate SQL is never executed by Monitor;
- sensitive keys such as passwords, secrets and tokens are redacted;
- sensitive values are never included in candidate SQL;
- the page sends `X-Robots-Tag: noindex, nofollow, noarchive`.

## Implementation note

Monitor does **not** re-execute `siteconfig.php`.

Geeklog already loaded the active site configuration before Monitor runs. Monitor reads the file only to identify the `$_CONF[...]` keys assigned there, then uses the current runtime `$_CONF` values for those keys.

This avoids side effects from loading the configuration file a second time and keeps the audit aligned with the active multisite host selected by Geeklog.

## Access

Open Monitor administration and select **Configuration audit**.

The implementation targets the same modernization matrix as Monitor 1.4.0:

- Geeklog 2.1.1 through 2.2.2;
- PHP 5.6 through 8.1.
