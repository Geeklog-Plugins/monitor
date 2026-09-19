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


## Shared capability declaration

Monitor implements the memorandum capability convention through:

```php
plugin_getcapabilities_monitor()
```

It declares the provider roles:

```text
diagnostic
service
```

and the capabilities:

```text
monitor.health
monitor.diagnostics
monitor.logs.summary
monitor.plugins.status
dashboard.summary
```

These identifiers advertise existing Monitor-owned data and do not bypass permissions. Agent, Eclipse, Hub and future consumers should discover the declaration and then invoke the documented Monitor services. They must not maintain a parallel Monitor capability registry.

### Capability-to-service mapping

| Capability | Geeklog service | Purpose |
|---|---|---|
| `monitor.health` | `monitor / get_status` | Current site-health state and checks |
| `monitor.diagnostics` | `monitor / get_changes` and Root-only `get_configuration_audit` where appropriate | Operational change/configuration diagnostics |
| `monitor.logs.summary` | `monitor / get_log_summary`, `get_log_archives` | Bounded archived-log summaries |
| `monitor.plugins.status` | `monitor / get_plugins` | Installed/code/remote plugin status |
| `dashboard.summary` | `monitor / dashboard_summary` | Compact local summary for administrative dashboards |

## Content lifecycle observation

Monitor listens to Geeklog's native `PLG_itemSaved()` and `PLG_itemDeleted()` notifications through:

```php
plugin_itemsaved_monitor($id, $type, $old_id = '', $sub_type = '')
plugin_itemdeleted_monitor($id, $type, $sub_type = '')
```

The journal is observational only. Monitor does not copy item content and does not become the owner of another plugin's data. Each event stores only:

- timestamp;
- `saved` or `deleted`;
- item type;
- item id;
- optional subtype;
- previous id when Geeklog reports an id change on save.

The journal is stored below `path_data` with per-site isolation and is bounded to the most recent 30 days and at most 500 events. Concurrent notifications are serialized with an exclusive file lock.

This activity is exposed inside `monitor.get_changes` for the interval covered by the two snapshots being compared. Hub should still consume lifecycle notifications directly for its own relationship graph; Monitor's journal is an operational observation source for diagnostics, Connector and administrative presentation.

Monitor deliberately does not implement `plugin_getiteminfo_monitor()`: Monitor does not own normal Geeklog content objects, so its diagnostics and archives are better represented as services than as fake content items.

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


## `dashboard.summary`

Geeklog action: `dashboard_summary`

Returns a compact local-only operational summary intended for Eclipse and other capability-aware administrative consumers. It combines Monitor's current health summary with local plugin alignment counts and deliberately disables remote repository checks so dashboard rendering remains bounded and does not depend on an external request.

The service requires `monitor.admin` and remains read-only.

Typical consumers:

- Eclipse provider card;
- Agent administrative diagnostics when the caller is authorized;
- Hub administrative interoperability/integrity views.

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

Returns the comparison between the two most recent Monitor snapshots, including environment/plugin/storage changes, bounded `error.log` delta signatures, and content lifecycle activity observed between the two snapshot timestamps.

`content_activity` contains:

```php
array(
    'saved' => 12,
    'deleted' => 1,
    'count' => 13,
    'items' => array(...),
    'retention_days' => 30,
    'max_events' => 500
)
```

The returned item list is bounded to 100 events for one comparison. The underlying journal remains bounded to 500 events / 30 days.

The service does not capture a new snapshot. It remains strictly read-only.

Typical consumers:

- Connector: "what changed since the previous snapshot?";
- Hub diagnostics;
- Eclipse administrative change indicator.

## `monitor.get_plugins`

Geeklog action: `get_plugins`

Returns installed plugins with:

- installed version recorded in `gl_plugins.pi_version`;
- local code version reported through Geeklog's native plugin metadata APIs;
- normalized local alignment state;
- `upgrade_required` when the local code is newer than the recorded database version;
- enabled/disabled state;
- Geeklog requirement;
- latest known GitHub version when remote metadata is enabled;
- normalized remote version state;
- public repository/version URLs when known;
- `distribution_source` with `standalone` or `core`;
- `core_geeklog_baseline` for plugins bundled in the Geeklog Core repository.

Typical item:

```php
array(
    'name'                => 'monitor',
    'installed_version'   => '1.3.1',
    'code_version'        => '1.4.0',
    'upgrade_required'    => true,
    'local_version_state' => 'upgrade_required',
    'enabled'             => true,
    'geeklog_requirement' => '2.1.1',
    'distribution_source' => 'standalone',
    'latest_version'      => 'v1.4.0',
    'version_state'       => 'current',
    'repository_url'      => 'https://github.com/example/monitor',
    'version_url'         => 'https://github.com/example/monitor/releases/tag/v1.4.0'
)
```

### Geeklog Core plugins

Official plugins bundled directly in `Geeklog-Core/geeklog/plugins/` stay in the same installed-plugin list. Monitor currently recognizes:

- `calendar`;
- `links`;
- `polls`;
- `recaptcha`;
- `spamx`;
- `staticpages`;
- `xmlsitemap`.

These items expose `distribution_source = core`. Their local metadata is read from the installed plugin's `autoinstall.php` without executing it. Remote metadata is read from the corresponding `Geeklog-Core/geeklog/plugins/<name>/autoinstall.php` path.

A newer Core-bundled plugin is reported as `version_state = core_update`, meaning that the newer plugin is delivered with a Geeklog Core update rather than as an independent plugin deployment. Consumers must not present this as a normal standalone plugin update.

For Core plugins, `core_geeklog_baseline` reports the Geeklog requirement declared by the current Core version.

The service deliberately separates two different operational states:

1. **Local upgrade required** — the files already present on the server declare a version newer than the version stored in the Geeklog plugins table. Geeklog's native plugin manager must run the plugin upgrade/migrations.
2. **Remote update available** — GitHub exposes a version newer than the code currently present on the server. This is informational only; Monitor does not install it.

When a local code version is available, remote comparison uses that code version rather than the database version. This prevents a plugin whose new files are already copied but whose database upgrade is still pending from being reported simultaneously as the same remote update.

Local states include:

- `current`;
- `upgrade_required`;
- `installed_ahead`;
- `code_missing`;
- `callback_unavailable`;
- `version_unavailable`;
- `code_version_invalid`;
- `installed_version_invalid`.

For disabled plugins, Geeklog may not have loaded the plugin callback. Monitor therefore uses `PLG_getParams()` as a read-only metadata fallback when possible instead of including plugin code itself.

Optional argument:

```php
array('include_remote' => false)
```

Remote metadata is enabled by default. Monitor uses its normal cache and never force-refreshes GitHub metadata from this service.

The summary explicitly keeps the two counts separate:

```php
array(
    'installed'         => 12,
    'enabled'           => 11,
    'disabled'          => 1,
    'upgrades_required' => 2,
    'updates_available' => 4,
    'core_updates_available' => 1
)
```

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

- Hub may include Monitor status/change signals in its own integrity context but should not parse Monitor logs or snapshots. Hub should continue to listen to Geeklog lifecycle events directly when it needs them for relationship maintenance.
- Connector may expose these services externally after applying its own authentication/authorization policy but should not read Monitor storage directly.
- Eclipse may present concise administrator-facing status but should not contain Monitor diagnostic business logic. Local plugin upgrades belong in its attention area, while remote releases belong in an informational updates card.
