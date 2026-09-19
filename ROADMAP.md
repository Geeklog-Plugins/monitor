# Monitor 1.4.0 Roadmap

## Vision

Monitor 1.4.0 is a modernization and simplification release.

The objective is not to turn Monitor into a collection of unrelated administration tools. Monitor should become the reference Geeklog plugin for **site health, diagnostics, operational security monitoring, alerts and actionable recommendations**.

The guiding model is:

> **Observe -> Diagnose -> Alert -> Recommend -> Act only when explicitly requested**

Monitor should prefer read-only observation and diagnostics. State-changing actions must be deliberate, permission-checked, CSRF-protected and narrowly scoped.

Compatibility target:

- Geeklog **2.1.1 through 2.2.2**
- PHP **5.6 through 8.1**

New code must use the common safe subset of those versions unless Monitor explicitly changes its support policy later.

---

# Current stabilization status

The 1.4.0 modernization baseline has now been validated on real Geeklog installations.

Validated in September 2026:

- [x] Monitor installs and operates on Geeklog 2.1.1;
- [x] Monitor admin pages operate on Geeklog 2.2.2;
- [x] Monitor Configuration opens correctly on Geeklog 2.2.2;
- [x] the Geeklog 2.2.x `COM_createHTMLDocument()` rendering path is used instead of removed `COM_siteHeader()` / `COM_siteFooter()` APIs;
- [x] the native configuration hierarchy follows the official Polls pattern: `subgroup -> tab -> fieldset -> settings`;
- [x] text configuration fields use `selection_array = NULL` rather than an invalid numeric selection id;
- [x] existing malformed Monitor 1.4.0 configuration rows can be repaired idempotently before Geeklog builds the configuration UI;
- [x] configuration language metadata is available to the Geeklog 2.2.x autocomplete/configuration UI;
- [x] PHP 5.6 syntax CI passes;
- [x] PHP 8.1 syntax CI passes;
- [x] security/API regression guard passes;
- [x] installable archive generation passes;
- [x] the generated archive excludes dotfiles and dot-directories;
- [x] the generated archive is committed under `dist/` and also published as a GitHub Actions artifact.

Still requiring dedicated validation before declaring every release gate complete:

- [x] Ban-present runtime integration test;
- [x] complete scheduled-task runtime test;
- [x] two-site shared-files/multisite upgrade test;
- [ ] interrupted database migration/retry test on a disposable installation.

The Geeklog configuration lessons discovered during this work have also been incorporated into the development memorandum, using the official Polls plugin and Geeklog `ConfigInterface`/`config.class.php` as reference implementations.

---

## Design principles

### 1. Monitor first, modify second

The dashboard should explain what is wrong before offering to change anything. Automatic changes to plugin files, logs, images, configuration or security state should not happen simply because Monitor detected an issue.

### 2. Remove historical duplication

Monitor should remove or reduce functionality that overlaps with Geeklog Core or specialized plugins where a maintained API or dedicated plugin is the better owner.

### 3. Small, testable responsibilities

Large controllers should progressively be split into focused helpers/services while keeping the plugin understandable and PHP 5.6 compatible. Avoid creating a framework inside the plugin.

### 4. Safe by default

- validate all input;
- escape output for its rendering context;
- use Geeklog ACL checks for privileged operations;
- use Geeklog CSRF tokens for state-changing actions;
- require POST for mutations;
- keep TLS certificate verification enabled;
- do not trust proxy-supplied IP headers by default;
- never block PHP workers with artificial sleeps;
- do not silently transmit site information to third parties.

### 5. Multisite-aware

Plugin files may be shared while database/configuration state is site-specific. Monitor must operate only in the active site's context, derive paths from active `$_CONF`, avoid modifying sibling sites, tolerate previous persisted state during upgrades and keep migrations restartable.

---

# Positioning: Monitor and Ban

Monitor must not become a second full Ban plugin.

## Monitor owns

- health and diagnostic checks;
- security observations and counters;
- log/event correlation;
- severity classification;
- alerts and recommendations;
- Ban capability/status visibility;
- explicit requests to Ban through an isolated adapter when supported.

## Ban owns

- persistent allow/deny rules;
- IP/range/CIDR matching;
- User-Agent/Referer/Script blocking;
- whitelist semantics;
- TTL enforcement;
- external block-list enforcement;
- final access-denial decisions.

Ban remains optional because Monitor supports Geeklog 2.1.1 while current Ban releases require newer Geeklog versions.

During the 1.4.0 transition, legacy `monitor_ban` data remains readable, but Monitor no longer expands its own general-purpose automatic ban engine.

---

# Phase 0 - Baseline and regression inventory

- [x] inventory major administration actions and unsafe historical behaviour;
- [x] inventory filesystem/log/image operations;
- [x] inventory Monitor database/configuration state;
- [x] identify historical third-party telemetry;
- [x] identify hard-coded legacy integrations;
- [x] establish Geeklog/PHP compatibility targets;
- [x] add PHP 5.6 / 8.1 CI syntax coverage;
- [x] add security/API regression guards.

Runtime matrix:

| Geeklog | PHP | Status |
|---|---:|---|
| 2.1.1 | 5.6-compatible code | validated for Monitor installation/runtime |
| 2.1.1 | 7.x/8.x where Geeklog supports it | expected from common code path |
| 2.2.2 | 8.1 | validated for Monitor admin/configuration runtime |

---

# Phase 1 - Security baseline (P0)

## Client IP handling

- [x] use `REMOTE_ADDR` as the default source;
- [x] validate IPs with `FILTER_VALIDATE_IP`;
- [x] do not trust `HTTP_CLIENT_IP` / `X-Forwarded-For` automatically;
- [x] avoid interpolating unvalidated IP data into SQL;
- [ ] add trusted-proxy configuration only if a real requirement appears.

## Request blocking and mutations

- [x] remove historical `sleep(60)` behaviour;
- [x] use immediate denial for retained explicit legacy banned records;
- [x] keep expensive legacy scans out of frontend request paths;
- [x] use ACL checks on Monitor administration;
- [x] protect log clearing with POST + Geeklog CSRF token;
- [x] remove state-changing plugin deployment/update actions from the Monitor dashboard.

## Network/privacy

- [x] remove TLS verification bypass from the old updater path by removing that deployment path;
- [x] remove historical third-party upgrade telemetry email;
- [x] avoid automatic executable-code deployment from normal Monitor requests.

## Output safety

- [x] escape log content before HTML rendering;
- [x] bound log reads;
- [x] constrain selectable log files to the configured log directory.

---

# Phase 2 - PHP 5.6-8.1 correctness (P0)

- [x] initialize previously unsafe variables;
- [x] remove unquoted `fopen(..., a)` usage;
- [x] replace deprecated/unsafe historical date handling with compatibility helpers;
- [x] guard relevant superglobal accesses;
- [x] remove the obsolete image resize path containing the `$hreight` defect;
- [x] preserve PHP 5.6-compatible syntax;
- [x] PHP 5.6 lint passes in CI;
- [x] PHP 8.1 lint passes in CI.

---

# Phase 3 - Remove legacy attack surface and code (P0/P1)

- [x] remove bundled TimThumb;
- [x] remove `admin/timthumb-config.php`;
- [x] remove the legacy `SimpleImage` resize helper;
- [x] remove remote image-fetch/webshot responsibilities;
- [x] remove or isolate historical hard-coded plugin integrations that do not belong in Monitor;
- [x] introduce an isolated Ban adapter instead of Ban-table coupling.

Image management direction: Monitor detects oversized images and recommends action. It does not silently resize or convert source images.

---

# Phase 4 - Database, configuration and persistence modernization (P1)

## Legacy security table

- [x] preserve legacy `monitor_ban` data through the transition;
- [x] stop treating it as a general replacement for Ban;
- [x] migrate retained table to InnoDB where required;
- [x] add useful indexes;
- [x] make the 1.4.0 table migration idempotent/retryable;
- [ ] decide in a later release whether the legacy table can be retired completely.

## Geeklog native configuration

- [x] use `config::get_instance()` and `get_config('monitor')`;
- [x] use the official configuration hierarchy `sg_main -> tab_main -> fs_main -> settings`;
- [x] use `NULL` for `selection_array` when no selection list exists;
- [x] provide `$LANG_configsections`, `$LANG_configsubgroups`, `$LANG_tab`, `$LANG_fs` and `$LANG_confignames` metadata;
- [x] support `english.php` and `english_utf-8.php` loading paths;
- [x] repair previously persisted malformed 1.4.0 configuration rows on Geeklog 2.2.x;
- [x] validate the configuration page on Geeklog 2.2.2 with PHP warnings enabled.

The canonical implementation reference is the official Geeklog Polls plugin plus the core `ConfigInterface` and `config.class.php` contract.

---

# Phase 5 - Monitoring engine (P1)

Implemented baseline:

- [x] common health result/status model;
- [x] Geeklog version check;
- [x] PHP version check;
- [x] path checks;
- [x] disk-space diagnostic where available;
- [x] Monitor code/persisted-version visibility;
- [x] Ban capability/status check;
- [x] read-only oversized-image diagnostic with bounded scan;
- [ ] scheduled-task last-run state;
- [ ] HTTPS/site URL consistency diagnostic;
- [ ] stale obsolete-file diagnostic.

Oversized image scanning is bounded and read-only; a future detailed inventory should preferably be scheduled/cached rather than repeatedly scanning large image trees from the dashboard.

---

# Phase 6 - Reference dashboard (P1)

- [x] focused Overview view;
- [x] environment/health information;
- [x] Logs view;
- [x] read-only Plugins view;
- [x] homogeneous plugin cards: icon/name header, state badges on a dedicated row, compact two-column metadata;
- [x] use a single icon resolver (`plugin-icons.php`) and avoid duplicated server/client icon rendering;
- [x] keep requirement/compatibility presentation server-owned to avoid duplicate metadata blocks;
- [x] show compatibility badges only when an update is available, including explicit incompatible/unknown states;
- [x] show pre-install compatibility and declared Geeklog/PHP requirements on Discover plugin cards using remote `plugin.json` metadata;
- [x] Security view;
- [x] Ban capability visibility;
- [x] Geeklog 2.2.2-compatible rendering using `COM_createHTMLDocument()`;
- [x] real-runtime validation on Geeklog 2.1.1 and 2.2.2;
- [ ] move more presentation markup into `.thtml` only where it materially improves maintainability.

---

# Phase 7 - Safe log viewer (P1)

- [x] allowlisted log directory/files;
- [x] prevent arbitrary path access;
- [x] bounded tail reads;
- [x] HTML escape log content;
- [x] clear only by explicit POST + CSRF;
- [x] never clear a log merely because it was emailed;
- [x] render archive catalogue through Geeklog `ADMIN_simpleList()`;
- [x] sortable archive columns for date, log name and size;
- [x] open archive preview as a dedicated same-tab detail view with explicit back links;
- [ ] optional search/filter;
- [ ] optional severity filtering;
- [ ] optional explicit download action.

---

# Phase 8 - Alerts and state changes (P2)

Target design remains transition-based alerting rather than repeated spam:

```text
OK -> WARNING : notify
WARNING -> WARNING : no duplicate notification
WARNING -> ERROR : notify
ERROR -> OK : optional recovery notification
```

- [ ] persistent alert-state model;
- [ ] low disk alert;
- [ ] scheduled task staleness alert;
- [ ] rapidly growing error-log alert;
- [ ] security-event threshold alert;
- [ ] optional recovery notifications.

---

# Phase 9 - Plugin update advisor (P1/P2)

The old executable-code updater has been removed from the admin workflow.

- [x] show local plugin/version state read-only;
- [x] do not deploy code from a normal Monitor request;
- [ ] add safe remote release metadata discovery if operationally useful;
- [x] evaluate Geeklog/PHP compatibility metadata from remote `plugin.json`, with default-branch fallback when a release/tag manifest is unavailable;
- [ ] link to source/release and recommend an action.

A dedicated updater/deployment component remains preferable to rebuilding deployment responsibilities inside Monitor.

---

# Phase 10 - Scheduled tasks (P1)

Current scheduled work includes bounded legacy-observation cleanup and diagnostic table checks.

- [x] remove destructive log-email/clear behaviour;
- [x] keep scheduled work diagnostic rather than auto-repairing database tables;
- [ ] review/limit `CHECK TABLE ... FAST` scope and MySQL-specific cost;
- [ ] add scheduled health snapshot/cache if dashboard checks become expensive;
- [ ] complete runtime scheduled-task test on both target Geeklog generations.

---

# Phase 11 - Multisite and shared-files safety (P1)

- [x] derive active paths/configuration from the current site;
- [x] keep persisted state site-scoped;
- [x] make schema migration idempotent;
- [x] avoid requiring sibling sites to upgrade simultaneously at code level;
- [x] validate two sites sharing Monitor files with separate databases/configurations;
- [x] explicitly test upgrade of site A while site B still has previous persisted state.

---

# Phase 12 - UI, templates and maintainability (P1)

- [x] remove old monolithic/unsafe updater responsibilities from the admin page;
- [x] use `COM_createHTMLDocument()` for compatible rendering across 2.1.1-2.2.2;
- [x] isolate health and Ban capability logic into helpers;
- [x] keep PHP 5.6-compatible implementation style;
- [ ] further template/controller separation where it makes the plugin simpler rather than more abstract.

Current internal separation includes:

```text
functions.inc
lib/MonitorHealth.php
lib/MonitorCompat.php
lib/MonitorConfigCompat.php
lib/MonitorBanAdapter.php
admin/index.php
```

---

# Phase 13 - Installation and upgrade quality (P1)

- [x] code version set to 1.4.0 with real migration path;
- [x] minimum Geeklog metadata set to 2.1.1;
- [x] sequential migration logic;
- [x] idempotent schema changes;
- [x] do not mark 1.4.0 installed until required schema migration succeeds;
- [x] preserve previous data on migration failure;
- [x] remove historical telemetry;
- [x] fresh package build validated;
- [x] configuration compatibility validated on 2.1.1/2.2.2;
- [x] idempotently add missing `github_token` configuration rows for already-installed 1.4.0 sites when Configuration is opened, independently of site language;
- [ ] execute deliberate interrupted-migration/retry test.

---

# Phase 14 - Documentation and release quality (P1)

- [x] README rewritten around Monitor's current positioning;
- [x] upgrade validation document exists;
- [x] Geeklog API compatibility notes documented;
- [x] CI/build workflows documented by repository structure;
- [x] installable archive generated under `dist/`;
- [x] configuration API lessons contributed back to the Geeklog development memorandum;
- [x] add `RELEASE-NOTES-1.4.0.md` with the release summary and upgrade notes;
- [ ] consider `SECURITY.md` for public release maintenance expectations.

---


# Phase 15 - Shared capabilities and consumers (P1)

Monitor follows the shared capability contract in the development memorandum rather than creating Agent-, Eclipse- or Hub-specific APIs.

- [x] declare provider roles `diagnostic` and `service` through `plugin_getcapabilities_monitor()`;
- [x] declare `monitor.health`;
- [x] declare `monitor.diagnostics`;
- [x] declare `monitor.logs.summary`;
- [x] declare `monitor.plugins.status`;
- [x] declare `dashboard.summary`;
- [x] expose a compact read-only `dashboard.summary` service for capability-aware administrative consumers;
- [x] keep all service data permission-checked and provider-owned;
- [x] keep Agent, Eclipse and Hub as consumers rather than dependencies;
- [x] add root-level `plugin.json` metadata manifest;
- [x] document the service/capability mapping in `docs/SERVICES.md`;
- [ ] validate the capability-driven Eclipse card against Eclipse 1.2 when that consumer implementation is ready;
- [ ] validate Agent discovery against the current Agent development branch;
- [ ] validate Hub interoperability audit recognition when the Hub implementation reaches that phase.

The release of Monitor 1.4.0 is not blocked by future consumer implementations as long as the provider contract remains stable, read-only and covered by CI guardrails.

---

# Features explicitly not targeted for Monitor 1.4.0

Monitor 1.4.0 is not intended to become:

- a replacement WAF;
- a full replacement for Ban;
- a SIEM/log analytics platform;
- a backup system;
- a general deployment manager;
- a multisite control plane;
- a file manager;
- an image CDN/proxy;
- an external uptime service.

---

# 1.4.0 release gates

## Security

- [x] no retained state-changing GET action in the modernized admin workflow;
- [x] CSRF protection on retained mutations;
- [x] no TLS verification bypass in retained Monitor network/deployment behaviour;
- [x] no artificial request `sleep()` defence;
- [x] safe direct client-IP handling;
- [x] safe log HTML rendering;
- [x] no silent external telemetry.

## Compatibility

- [x] Monitor runtime tested on Geeklog 2.1.1;
- [x] Monitor admin/configuration runtime tested on Geeklog 2.2.2;
- [x] PHP 5.6 lint CI passes;
- [x] PHP 8.1 lint CI passes;
- [x] no known PHP 8 warning/fatal remains in the tested normal Monitor/configuration workflows;
- [x] two-site shared-files upgrade transition test.

## Simplification

- [x] TimThumb removed;
- [x] obsolete image resizing implementation removed;
- [x] obsolete integrations removed or isolated;
- [x] legacy ban behaviour reduced/transitioned;
- [x] expensive historical frontend scans removed;
- [x] dashboard responsibilities narrowed to monitoring/diagnostics.

## Interoperability

- [x] shared capability declaration present;
- [x] `dashboard.summary` is implemented as a bounded read-only service;
- [x] `plugin.json` metadata manifest is present;
- [x] service contracts for Agent/Eclipse/Hub are documented;
- [x] no consumer-specific dependency is introduced.

## Reference-quality monitoring

- [x] structured health checks;
- [x] useful overview dashboard;
- [x] safe log viewer baseline;
- [x] bounded scheduled maintenance baseline;
- [x] actionable recommendations;
- [x] Ban integration/status visibility;
- [ ] transition-based alert engine.

---

# Longer-term direction after 1.4.0

Potential future work should be driven by operational value rather than feature count:

- stable machine-readable health summary for connectors;
- lifecycle/security events through a common Geeklog capability/event contract;
- optional Hello notification integration;
- improved plugin release compatibility metadata;
- optional modernized Ban API integration;
- exportable support/diagnostic report;
- privacy-safe trend history.

## North star

A Geeklog administrator should be able to open Monitor and answer, within a few seconds:

1. **Is my site healthy?**
2. **What requires attention?**
3. **Is anything suspicious happening?**
4. **Are my plugins/runtime compatible and up to date?**
5. **What should I do next?**

If Monitor can answer those questions reliably, safely and with little operational overhead, it becomes a reference plugin in its category without becoming unnecessarily large.
