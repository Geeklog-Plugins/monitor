# Monitor 1.4.0 — Geeklog API compatibility audit

Target matrix:

- Geeklog 2.1.1 through 2.2.2
- PHP 5.6 through 8.1

This audit follows the Geeklog-Plugins memorandum and verifies the Geeklog APIs used by Monitor against the core implementation. Runtime testing on Geeklog 2.1.1 is also part of the release evidence.

## Rendering and admin UI

| API | Monitor use | 2.1.1 | 2.2.2 | Decision |
|---|---|---:|---:|---|
| `COM_createHTMLDocument()` | Final admin page rendering and access-denied page | yes | yes | Required common rendering API |
| `COM_siteHeader()` / `COM_siteFooter()` | Legacy rendering | legacy | removed | Forbidden in Monitor 1.4.0; CI rejects reintroduction |
| `COM_output()` | Sends the completed document | yes | yes | Keep |
| `COM_createLink()` | Admin navigation links | yes | yes | Keep |
| `COM_applyFilter()` | Small scalar request filters | yes | yes | Keep; validation still happens separately |
| `Template` | Plugin-local administration template | yes | yes, legacy-compatible | Keep for the 2.1.1–2.2.2 common subset; do not depend on 2.2.x-only template helpers |

`COM_createHTMLDocument()` was introduced long before Geeklog 2.1.1 and is the replacement required by Geeklog 2.2.x after removal of `COM_siteHeader()` and `COM_siteFooter()`.

## Security APIs

| API | Use | Status |
|---|---|---|
| `SEC_hasRights()` | Monitor admin ACL | common and retained |
| `SEC_createToken()` | CSRF token creation | common and retained |
| `SEC_checkToken()` | POST mutation validation | common and retained |
| `CSRF_TOKEN` | CSRF form field name | common and retained |

All Monitor state-changing admin operations remain POST + CSRF protected.

## Database and plugin APIs

| API | Use | Status |
|---|---|---|
| `DB_query()` | Monitor table reads/migrations | common and retained |
| `DB_getItem()` | Installed Monitor version | common and retained |
| `DB_checkTableExists()` | Idempotent schema migration | common and retained |
| `PLG_chkVersion()` | Plugin code version discovery | common and retained |
| `plugin_chkVersion_monitor()` | Monitor Plugin API callback | documented Plugin API |
| `plugin_upgrade_monitor()` | Monitor upgrade callback | documented Plugin API |
| `plugin_runScheduledTask_monitor()` | Scheduled diagnostics | documented Plugin API |
| `plugin_cclabel_monitor()` | Command & Control integration | documented Plugin API |
| `plugin_getadminoption_monitor()` | Admin menu integration | documented Plugin API |
| `plugin_geticon_monitor()` | Admin icon | documented Plugin API |

## Configuration API

Monitor uses:

- `config::get_instance()`
- `$c->get_config('monitor')`
- `$c->group_exists('monitor')`
- `$c->add(...)`

`group_exists()` is present in the Geeklog configuration class and the Monitor fresh installation path has also been runtime-tested successfully on Geeklog 2.1.1.

For Geeklog 2.2.x configuration compatibility, Monitor now supplies:

- `$LANG_configsections['monitor']`
- `$LANG_configsubgroups['monitor']`
- `$LANG_confignames['monitor']`
- `$LANG_fs['monitor']`

Fresh-install configuration calls explicitly pass `0` as the final tab selector to avoid ambiguous configuration metadata and follow the modern core examples.

## Compatibility rules enforced by CI

CI rejects the following known regressions:

- `COM_siteHeader(`
- `COM_siteFooter(`
- disabled TLS verification pattern previously used by the legacy updater
- `sleep(60)`
- the removed historical telemetry email address

CI also checks that the required Geeklog 2.2.x configuration-language arrays are present.

## Runtime release checks

Before tagging Monitor 1.4.0:

1. Geeklog 2.1.1: fresh install, dashboard, logs, security, plugin list, configuration page.
2. Geeklog 2.2.2: same checks.
3. Upgrade from an existing Monitor 1.3.x installation where available.
4. Verify `dist/monitor_1.4.0_2.1.1.zip` installs cleanly on both targets.
5. Verify the ZIP contains no path component beginning with `.`.

Known runtime evidence at the time of this audit: fresh install and Monitor administration confirmed working on Geeklog 2.1.1. Geeklog 2.2.2 exposed the removed `COM_siteHeader()` / `COM_siteFooter()` API usage; Monitor was corrected to `COM_createHTMLDocument()` and CI now prevents regression.
