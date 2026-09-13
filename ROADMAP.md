# Monitor 1.4.0 Roadmap

## Vision

Monitor 1.4.0 is a modernization and simplification release.

The objective is not to turn Monitor into a collection of unrelated administration tools. Monitor should become the reference Geeklog plugin for **site health, diagnostics, operational security monitoring, alerts and actionable recommendations**.

The guiding model is:

> **Observe -> Diagnose -> Alert -> Recommend -> Act only when explicitly requested**

Monitor should prefer read-only observation and diagnostics. State-changing actions must be deliberate, permission-checked, CSRF-protected and narrowly scoped.

The current modernization compatibility target follows the Geeklog development memorandum:

- Geeklog **2.1.1 through 2.2.2**
- PHP **5.6 through 8.1**

New code must use the common safe subset of those versions unless Monitor explicitly changes its support policy later.

---

## Design principles

### 1. Monitor first, modify second

The dashboard should explain what is wrong before offering to change anything.

Automatic changes to plugin files, logs, images, configuration or security state should not happen simply because Monitor detected an issue.

### 2. Remove historical duplication

Monitor currently contains functionality that overlaps with Geeklog Core and other plugins. Version 1.4.0 should remove or reduce duplicated functionality where a maintained Geeklog API or specialized plugin is the better owner.

### 3. Small, testable responsibilities

Large controller files should progressively be split into focused helpers/services while keeping the plugin understandable and compatible with PHP 5.6.

Avoid creating a framework inside the plugin.

### 4. Safe by default

- validate all input;
- escape output for its rendering context;
- use Geeklog ACL checks for all privileged operations;
- use Geeklog CSRF tokens for state-changing actions;
- require POST for state changes;
- keep TLS certificate verification enabled;
- avoid trusting proxy-supplied IP headers unless trusted proxies are explicitly configured;
- never block PHP workers with artificial sleeps;
- do not silently transmit site information to third parties.

### 5. Multisite-aware

Plugin files may be shared while database/configuration state is site-specific.

Monitor must:

- operate only in the active site's context;
- derive paths from the active `$_CONF` configuration;
- avoid modifying sibling sites;
- tolerate the previous persisted Monitor state until the active site completes its upgrade;
- keep upgrades restartable and non-destructive.

---

# Positioning: Monitor and Ban

## Do not build a second full Ban plugin inside Monitor

The existing Geeklog Ban plugin already provides a specialized enforcement engine with features such as:

- exact IP bans;
- IPv4 ranges and CIDR;
- regular-expression IP rules;
- HTTP Referer rules;
- User-Agent rules;
- Script Name rules;
- whitelist records;
- permanent and TTL-based bans;
- Stop Forum Spam integration;
- automatic banning based on GUS traffic history;
- ban logging and notifications.

Reimplementing these features in Monitor would increase complexity, duplicate security-sensitive code and work against the 1.4.0 simplification objective.

## Recommended responsibility split

### Monitor owns

- detection of suspicious or unhealthy conditions;
- security observations and counters;
- correlation of logs and recent events;
- health checks;
- diagnostics;
- severity classification;
- alerts;
- recommendations;
- visibility into Ban status when Ban is installed;
- requesting a ban through a stable Ban integration when explicitly configured.

### Ban owns

- persistent allow/deny rules;
- IP/range/CIDR matching;
- User-Agent/Referer/Script blocking;
- whitelist semantics;
- ban TTL enforcement;
- external block-list enforcement;
- final access-denial decision.

## Compatibility issue

Ban 2.0.5 currently requires Geeklog 2.2.1 or newer, while Monitor 1.4.0 targets Geeklog 2.1.1 through 2.2.2.

Monitor therefore **must not require Ban**.

On older Geeklog installations Monitor should still provide diagnostics, logging and alerts. A future modernization of Ban should be considered separately if compatibility with Geeklog 2.1.1-2.2.2 is desired.

## Monitor security fallback

Monitor should not keep its current general-purpose `monitor_ban` implementation as a competing ban engine.

During the 1.4.0 transition:

1. preserve existing installations and data;
2. stop expanding the legacy Monitor ban feature;
3. replace unsafe request-time behaviour with safe observation/counters;
4. when Ban is installed, prefer an explicit integration adapter;
5. when Ban is absent, alert/recommend rather than silently creating permanent blocking rules;
6. decide after migration testing whether the legacy `monitor_ban` table can be retired in a later release.

A very small emergency protection mechanism may remain only if it solves a Monitor-specific need that cannot be delegated safely. It must be temporary, bounded, IPv4/IPv6-safe, proxy-aware, non-blocking and disabled or conservative by default.

## Future Ban integration contract

Monitor must not query or mutate Ban tables directly.

Prefer a small capability adapter so Monitor can ask questions such as:

- is Ban installed and enabled?
- can this installation accept an IP ban request?
- is this IP currently allowed, blocked or unknown?
- can a temporary ban be requested with a reason and TTL?

The existing Ban helper/API can be used behind the adapter where available, but Monitor should not couple its architecture to a specific internal Ban function or SQL schema.

This leaves room to modernize Ban independently and expose a cleaner public capability later.

---

# Phase 0 - Baseline and regression inventory

Before adding features, document and test the existing Monitor behaviour.

- [ ] inventory all existing administration actions;
- [ ] inventory every state-changing request;
- [ ] inventory filesystem reads/writes/deletes;
- [ ] inventory database tables and configuration values;
- [ ] inventory network calls;
- [ ] inventory integrations with other plugins;
- [ ] identify functionality that can be removed rather than ported;
- [ ] identify code paths required only for historical plugins;
- [ ] establish a Geeklog/PHP compatibility test matrix.

Target matrix:

| Geeklog | PHP | Expected |
|---|---:|---|
| 2.1.1 | 5.6 | supported |
| 2.1.1 | 7.x | supported |
| 2.2.2 | 7.x | supported |
| 2.2.2 | 8.1 | supported |

---

# Phase 1 - Security baseline (P0)

Release-blocking work.

## Client IP handling

- [ ] use `REMOTE_ADDR` as the default source of the client IP;
- [ ] validate addresses with `FILTER_VALIDATE_IP`;
- [ ] do not trust `HTTP_CLIENT_IP` or `X-Forwarded-For` by default;
- [ ] add optional trusted-proxy configuration only if necessary;
- [ ] support IPv4 and IPv6 wherever possible;
- [ ] never interpolate unvalidated IP values directly into SQL.

## Remove request blocking

- [ ] remove all `sleep(60)` calls from ban/security request paths;
- [ ] use an immediate `403` or `429` where blocking is still required;
- [ ] move expensive processing out of frontend requests.

## CSRF and HTTP methods

- [ ] inventory all mutations;
- [ ] require POST for mutations;
- [ ] add `SEC_createToken()` / `SEC_checkToken()` protection;
- [ ] retain independent `monitor.admin` ACL checks on every privileged endpoint.

Actions include at least:

- clearing/rotating logs;
- resizing images;
- uploading/changing user photos;
- plugin update/install operations if retained;
- future security actions and ban requests.

## TLS/network security

- [ ] remove every `CURLOPT_SSL_VERIFYPEER = false`;
- [ ] enable peer and host verification;
- [ ] define connection and total timeouts;
- [ ] handle GitHub/API failures without warnings or partial state changes;
- [ ] avoid downloading executable code unless explicitly requested by an administrator.

## Output safety

- [ ] escape log contents before HTML rendering;
- [ ] escape filenames, plugin metadata and remote API values according to context;
- [ ] ensure remote error messages cannot inject admin HTML/JavaScript.

## Privacy

- [ ] remove the historical automatic upgrade notification email to a third-party address;
- [ ] document every optional outbound request;
- [ ] do not transmit site URL/name/version information without explicit configuration.

---

# Phase 2 - PHP 5.6-8.1 correctness (P0)

- [ ] initialize variables and arrays before use;
- [ ] fix unquoted `fopen(..., a)` modes;
- [ ] remove/rework deprecated `strftime()` use;
- [ ] fix `$hreight` typo and image dimension checks;
- [ ] guard access to `$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER`, `$_FILES` keys;
- [ ] check native function return values before using them;
- [ ] remove warnings caused by PHP 8 stricter argument handling;
- [ ] keep syntax PHP 5.6 compatible;
- [ ] use Geeklog-compatible date/log helpers when available with safe fallback.

---

# Phase 3 - Remove legacy attack surface and code (P0/P1)

## Remove TimThumb

- [ ] remove bundled TimThumb from `admin/images.php`;
- [ ] remove `admin/timthumb-config.php` if no longer used;
- [ ] use a small local image resize helper or Geeklog image facilities instead;
- [ ] do not support remote image fetching/webshots as part of Monitor.

## Remove dead/historical integrations

Review direct special cases for:

- Sphere;
- DokuWiki;
- MediaGallery;
- Classifieds;
- PayPal;
- other legacy plugins.

For each integration:

- [ ] keep only if it serves a current Monitor responsibility;
- [ ] otherwise remove it;
- [ ] avoid hard-coded knowledge of another plugin's private tables/files;
- [ ] prefer a capability/API adapter when integration is still useful.

---

# Phase 4 - Database and persistence modernization (P1)

## `monitor_ban` transition

- [ ] keep legacy data readable during the upgrade window;
- [ ] stop using the table as a general duplicate of Ban;
- [ ] determine whether security counters/events need a new neutral storage model;
- [ ] do not delete the legacy table until migration is verified and a later release explicitly retires it.

If the table is temporarily retained:

- [ ] migrate MyISAM to InnoDB where supported;
- [ ] add useful indexes for `bantype`, `data` and `created`;
- [ ] validate/cast/escape all values;
- [ ] make migrations idempotent and restartable.

## Event/history storage

Consider a small site-scoped history table only if it materially improves Monitor.

Possible fields:

- timestamp;
- severity (`info`, `warning`, `error`, `security`);
- check/event identifier;
- short message;
- structured context where practical;
- resolved state where useful.

Retention must be configurable and bounded.

---

# Phase 5 - Monitoring engine (P1)

Introduce a small common result structure for health checks.

Example conceptual result:

```text
id: logs.error_size
status: warning
summary: error.log is larger than the configured threshold
value: 18 MB
recommendation: inspect or rotate the log
```

Standard states:

- `ok`
- `info`
- `warning`
- `error`

Checks should be independent and fail gracefully.

Initial checks:

- [ ] Geeklog version;
- [ ] PHP version;
- [ ] database connection/type/version where safely available;
- [ ] writable data/log/image paths;
- [ ] log file sizes and recent errors;
- [ ] scheduled-task last-run state where determinable;
- [ ] plugin version/update visibility;
- [ ] Monitor code version vs persisted version;
- [ ] disk free space where safely available;
- [ ] configured image library availability;
- [ ] HTTPS/site URL consistency;
- [ ] stale/obsolete Monitor files after upgrade;
- [ ] Ban plugin availability/status when installed.

Do not treat an unavailable optional capability as a site failure.

---

# Phase 6 - Reference dashboard (P1)

Create a focused administration dashboard rather than a long page of unrelated tools.

Suggested sections:

## Overview

- global state;
- number of warnings/errors;
- last Monitor scheduled run;
- last significant event.

## Environment

- site context;
- Geeklog version;
- PHP version;
- database information;
- active paths/state relevant to diagnostics.

## Logs

- file;
- size;
- recent warning/error count where practical;
- last modification;
- safe viewer link.

## Plugins

- installed version;
- code version;
- available upstream version where known;
- dependency/compatibility state;
- recommendation.

## Security

- suspicious-event counts;
- Monitor security observations;
- Ban installed/enabled status;
- recent Ban activity if exposed through a safe integration;
- recommendations.

---

# Phase 7 - Safe log viewer (P1)

Replace whole-file loading and destructive email-and-clear behaviour.

- [ ] list only files located in the configured log directory;
- [ ] prevent path traversal;
- [ ] show tail 50/100/500 lines without reading huge files unnecessarily;
- [ ] HTML-escape every displayed line;
- [ ] optional search/filter;
- [ ] optional severity filter where recognizable;
- [ ] allow download when permitted;
- [ ] rotation/clear only by explicit POST + CSRF;
- [ ] never automatically delete a log merely because it was emailed;
- [ ] handle large files safely.

---

# Phase 8 - Alerts and state changes (P2)

Monitor should alert on meaningful transitions, not continuously repeat the same problem.

Examples:

```text
OK -> WARNING : notify
WARNING -> WARNING : no duplicate notification
WARNING -> ERROR : notify
ERROR -> OK : recovery notification (optional)
```

Potential alerts:

- low disk space;
- scheduled task failure/staleness;
- rapidly growing error log;
- repeated authentication/CAPTCHA/security events;
- plugin update becoming available;
- important compatibility problem.

Email remains optional. The design should allow future consumers without coupling Monitor directly to Hello or another plugin.

---

# Phase 9 - Plugin update advisor (P1/P2)

Modernize the current update feature around **advice first**.

Default behaviour:

1. discover current installed/code version;
2. discover available release metadata;
3. evaluate known Geeklog/PHP compatibility when possible;
4. show release/source link;
5. recommend an action.

Do not automatically deploy executable code during a normal Monitor page request.

If direct installation is retained as an advanced feature:

- [ ] disabled by default or clearly separated;
- [ ] explicit administrator POST;
- [ ] CSRF protected;
- [ ] TLS verification mandatory;
- [ ] archive validation;
- [ ] path validation;
- [ ] rollback on extraction/deployment failure;
- [ ] shared-files/multisite warning;
- [ ] never assume all sites sharing files have already migrated their persistent state.

A future dedicated updater/deployment component may be a better long-term owner.

---

# Phase 10 - Scheduled tasks (P1)

Move maintenance work away from frontend requests.

Candidates:

- retention cleanup;
- security/event aggregation;
- log statistics;
- database health checks that should not run per request;
- update metadata refresh;
- stale temporary-data cleanup.

Scheduled work must be bounded and avoid expensive full-table/full-filesystem scans on every run.

---

# Phase 11 - Multisite and shared-files safety (P1)

- [ ] all persistent data remains site-scoped;
- [ ] configuration belongs to the active site;
- [ ] no sibling database/filesystem scanning;
- [ ] code 1.4.0 remains safe while a site's persisted Monitor state is still 1.3.x;
- [ ] upgrade one site without requiring immediate upgrade of every site sharing files;
- [ ] record/diagnose current-site code/state mismatches;
- [ ] test at least two Geeklog sites sharing Monitor files with separate databases/configurations.

Monitor may report that shared files are in use when this can be determined safely, but it must not become a multisite control plane.

---

# Phase 12 - UI, templates and maintainability (P1)

- [ ] move substantial presentation markup into `.thtml` templates;
- [ ] keep business logic out of templates;
- [ ] use `COM_createHTMLDocument()` where supported with a compatibility fallback for older Geeklog;
- [ ] use Geeklog CSS/JS registration APIs where compatible;
- [ ] avoid inline JavaScript generated from unescaped PHP values;
- [ ] split oversized admin controllers into focused helpers without overengineering;
- [ ] centralize common validation and rendering helpers.

Suggested internal separation (names are illustrative, not mandatory):

```text
functions.inc
lib/MonitorHealth.php
lib/MonitorLogs.php
lib/MonitorSecurity.php
lib/MonitorUpdates.php
lib/MonitorBanAdapter.php
admin/index.php
admin/logs.php (optional)
templates/*.thtml
```

PHP 5.6 compatibility must be preserved; namespaces/type declarations are not required for this modernization.

---

# Phase 13 - Installation and upgrade quality (P1)

- [ ] set Monitor code version to 1.4.0 only when migrations are ready;
- [ ] define accurate Geeklog compatibility metadata;
- [ ] sequential upgrade path from existing releases;
- [ ] idempotent checks before creating/changing persisted state;
- [ ] do not mark 1.4.0 installed until required migrations succeed;
- [ ] preserve previous data on failure;
- [ ] remove historical third-party upgrade telemetry email;
- [ ] clean obsolete files only when safe;
- [ ] test fresh install and upgrade separately.

---

# Phase 14 - Documentation and release quality (P1)

Rewrite the README around the new positioning.

Documentation should include:

- [ ] purpose and non-goals;
- [ ] compatibility matrix;
- [ ] installation/upgrade instructions;
- [ ] security model;
- [ ] trusted-proxy guidance if supported;
- [ ] Ban integration behaviour;
- [ ] scheduled-task behaviour;
- [ ] log handling and retention;
- [ ] multisite/shared-files considerations;
- [ ] privacy/outbound network calls;
- [ ] troubleshooting;
- [ ] migration notes for legacy `monitor_ban` users.

Consider adding:

- `CHANGELOG.md`
- `SECURITY.md`
- lightweight test/check scripts compatible with the project policy.

---

# Features explicitly not targeted for Monitor 1.4.0

To keep the release focused, Monitor 1.4.0 should not attempt to become:

- a replacement web application firewall;
- a complete replacement for the Ban plugin;
- a full SIEM/log analytics platform;
- a backup system;
- a deployment manager for every plugin;
- a multisite administration manager;
- a generic file manager;
- an image CDN/proxy;
- an external uptime monitoring service.

These may integrate with Monitor later, but should not inflate the core plugin.

---

# Proposed 1.4.0 release gates

Monitor 1.4.0 should not be released until all of the following are true:

## Security

- [ ] no state-changing GET actions;
- [ ] CSRF protection on mutations;
- [ ] no TLS verification bypass;
- [ ] no artificial request `sleep()` defence;
- [ ] safe client-IP handling;
- [ ] safe log HTML rendering;
- [ ] no silent external telemetry.

## Compatibility

- [ ] tested on Geeklog 2.1.1 and 2.2.2;
- [ ] tested on PHP 5.6 and PHP 8.1 at minimum;
- [ ] no known PHP 8 warnings/fatals in normal Monitor workflows;
- [ ] safe shared-files transition from the previous persisted state.

## Simplification

- [ ] TimThumb removed;
- [ ] obsolete integrations removed or isolated;
- [ ] legacy ban behaviour reduced/transitioned;
- [ ] expensive frontend scans removed;
- [ ] dashboard responsibilities clearly separated.

## Reference-quality monitoring

- [ ] structured health checks;
- [ ] useful overview dashboard;
- [ ] safe log viewer;
- [ ] scheduled maintenance;
- [ ] actionable recommendations;
- [ ] clear Ban integration/status visibility.

---

# Longer-term direction after 1.4.0

Possible future work should be driven by real operational value rather than feature count.

Candidates include:

- stable machine-readable health summary for future connectors;
- lifecycle/security event exposure through a common Geeklog event contract;
- optional Hello notification integration;
- improved plugin-release compatibility metadata;
- optional integration with a modernized Ban API;
- exportable diagnostic report for support/debugging;
- privacy-safe trend history.

The 1.4.0 architecture should make these possible without requiring them now.

---

## North star

A Geeklog administrator should be able to open Monitor and answer, within a few seconds:

1. **Is my site healthy?**
2. **What requires attention?**
3. **Is anything suspicious happening?**
4. **Are my plugins/runtime compatible and up to date?**
5. **What should I do next?**

If Monitor can answer those questions reliably, safely and with little operational overhead, it becomes a reference plugin in its category without becoming unnecessarily large.