# Monitor read-only services

Monitor 1.4.0 exposes structured diagnostics through Geeklog's native `PLG_invokeService()` dispatcher. The service layer is intended for trusted Geeklog consumers such as Hub, Connector and administrative presentation layers such as Eclipse.

## Design rules

- All Monitor services are read-only.
- No service exposes filesystem paths, configuration values, SQL alignment statements or log archive paths.
- Normal Monitor services require `monitor.admin`.
- `monitor.get_configuration_audit` preserves the existing Configuration Audit boundary and requires membership in the Geeklog `Root` group.
- Responses use a stable envelope with `provider`, `schema_version`, `service`, `generated_at` and `data`.
- Consumers should use `PLG_invokeService()` rather than call Monitor implementation helpers directly.
- Consumers must not read Monitor tables, JSON snapshots or archive files directly.

## Invocation

Example:

```php
$args = array();
$output = array();
$svc_msg = array();

$ret = PLG_invokeService('monitor', 'get_status', $args, $output, $svc_msg);
if ($ret == PLG_RET_OK) {
    // Use $output['data'].
}
```

Conceptual service names use `monitor.<action>` while Geeklog invocation uses the plugin and action separately.

## `monitor.get_status`

Geeklog action: `get_status`

Returns the current Monitor health summary and normalized checks.

Important privacy behavior:

- path checks are reported as `available` or `needs attention`;
- configured filesystem paths are not returned.

Typical consumers:

- Eclipse admin status widget;
- Connector site-health tool;
- Hub administrative integrity summary.

## `monitor.get_changes`

Geeklog action: `get_changes`

Returns the comparison between the two most recent Monitor snapshots, including environment/plugin/storage changes and bounded `error.log` delta signatures.

It does not capture a new snapshot. The service remains strictly read-only.

Typical consumers:

- Connector: "what changed since the previous snapshot?";
- Hub diagnostics;
- Eclipse administrative change indicator.

## `monitor.get_plugins`

Geeklog action: `get_plugins`

Returns installed plugins with:

- installed version;
- enabled/disabled state;
- Geeklog requirement;
- latest known GitHub version when remote metadata is enabled;
- normalized version state;
- public repository/version URLs when known.

Optional argument:

```php
array('include_remote' => false)
```

Remote metadata is enabled by default. Monitor uses its normal cache and never force-refreshes GitHub metadata from this service.

## `monitor.get_log_summary`

Geeklog action: `get_log_summary`

Returns bounded statistics for one archived day:

- archive count and total bytes;
- lines scanned;
- issue lines;
- top normalized `error.log` patterns;
- per-log statistics;
- secure Monitor admin URLs for viewing archives.

Optional argument:

```php
array('date' => '2026-09-12')
```

Without a date, the newest archived date is used. Statistics use the same bounded analysis as the daily Monitor email and may report `partial=true` for very large logs.

## `monitor.get_log_archives`

Geeklog action: `get_log_archives`

Returns the logical archive catalogue without filesystem paths.

Optional arguments:

```php
array(
    'date' => '2026-09-12',
    'limit' => 90
)
```

`limit` is constrained to 1..365 records.

Each returned item contains the date, logical log name, archive filename, size and secure Monitor admin view URL.

## `monitor.get_configuration_audit`

Geeklog action: `get_configuration_audit`

Root-only service. Returns only diagnostic state:

- whether the audit is consistent;
- whether `siteconfig.php` was readable;
- summary counts;
- key name, status, level, sensitivity marker and path-existence state.

It deliberately does **not** expose:

- `siteconfig.php` path;
- file or database values;
- effective values;
- generated SQL;
- filesystem path values.

## Response envelope

Example:

```php
array(
    'provider' => 'monitor',
    'schema_version' => 1,
    'service' => 'monitor.get_status',
    'generated_at' => 1789270000,
    'data' => array(...)
)
```

Consumers should branch on `schema_version` if a future Monitor release introduces an incompatible response contract.

## Intended ownership

Monitor remains authoritative for operational diagnostics. Consumers should not duplicate its logic:

- Hub may include Monitor status/change signals in its own integrity context but should not parse Monitor logs or snapshots.
- Connector may expose these services externally after applying its own authentication/authorization policy but should not read Monitor storage directly.
- Eclipse may present concise administrator-facing status but should not contain Monitor diagnostic business logic.
