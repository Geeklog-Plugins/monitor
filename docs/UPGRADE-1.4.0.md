# Monitor 1.4.0 Upgrade Validation

This document defines the validation procedure for the Monitor 1.4.0 stabilization phase and records the current validation status.

The objective is to verify the modernization baseline before adding further features.

## Supported modernization target

- Geeklog 2.1.1 through 2.2.2
- PHP 5.6 through 8.1
- MySQL/MariaDB installations supported by the current Monitor schema

The GitHub workflow performs syntax checks on PHP 5.6 and PHP 8.1 and guards against known unsafe or incompatible historical patterns.

### Current validated status — September 2026

The following have been validated successfully:

- Monitor installation/runtime on Geeklog 2.1.1;
- Monitor administration pages on Geeklog 2.2.2;
- Monitor Configuration page on Geeklog 2.2.2 with Root Debugging enabled;
- Geeklog 2.2.x rendering through `COM_createHTMLDocument()`;
- native configuration hierarchy compatible with the official Polls plugin;
- repair of malformed persisted Monitor configuration rows created during early 1.4.0 development;
- PHP 5.6 lint workflow;
- PHP 8.1 lint workflow;
- security/API regression guard;
- installable distribution archive generation.

The configuration API rules discovered during this validation have been incorporated into the Geeklog development memorandum.

---

## 1. Back up before upgrade testing

Before testing an existing Monitor installation, back up:

- the Geeklog database;
- the current Monitor plugin directory;
- Geeklog log files if they are needed for comparison.

Do not use a production site as the first upgrade test.

---

## 2. Fresh-install test

Install Monitor 1.4.0 on a clean supported Geeklog installation.

Expected plugin metadata:

```text
Monitor code version: 1.4.0
Minimum Geeklog version: 2.1.1
```

Confirm that the `monitor_ban` table exists with:

```text
ENGINE=InnoDB
bantype varchar(40)
data varchar(255)
created datetime
access int unsigned
```

Expected indexes:

```text
monitor_bantype_data (bantype, data prefix)
monitor_created (created)
```

The legacy table exists only for migration compatibility and short-lived Monitor security observations. It is not the future replacement for the dedicated Ban plugin.

### Native Geeklog configuration structure

A fresh 1.4.0 installation must create the configuration hierarchy using the same pattern as official Geeklog plugins such as Polls:

```text
sg_main
  -> tab_main
      -> fs_main
          -> emails
          -> repository
```

For fields such as `emails` and `repository`, which use the `text` type and no selection list, the `selection_array` argument passed to `config::add()` must be `NULL`.

Do not pass `0` for an absent selection array. In Geeklog 2.2.2, a numeric value is treated as an index into `$LANG_configselects`, which can produce `Undefined array key 0` in `_get_extended()`.

The final argument of `config::add()` is the **tab id**, not the subgroup.

### Status

- [x] Fresh Monitor 1.4.0 configuration structure corrected to the Polls/core pattern.
- [x] Configuration page validated on Geeklog 2.2.2.

---

## 3. Upgrade test: Monitor 1.3.1 -> 1.4.0

Start from a working Monitor 1.3.1 installation containing representative legacy rows in `monitor_ban`.

Deploy the 1.4.0 files, but before running the Geeklog plugin upgrade confirm that normal site requests still load.

This validates the shared-files rule:

> new files must not assume that the active site's persisted plugin state is already upgraded.

Run the normal Geeklog plugin upgrade.

Expected results:

1. existing `monitor_ban` rows are preserved;
2. the table engine is InnoDB;
3. `access` is an unsigned integer counter;
4. the two 1.4.0 indexes exist;
5. the plugin database version becomes `1.4.0` only after the migration succeeds;
6. `pi_gl_version` becomes `2.1.1`;
7. no third-party upgrade telemetry email is sent.

---

## 4. Existing early 1.4.0 configuration repair

Some development builds of Monitor 1.4.0 could persist malformed configuration rows before the final Geeklog 2.2.2 configuration rules were identified.

Typical symptoms under Root Debugging were:

```text
Undefined array key "monitor"
```

from `_UI_autocomplete_data()`, or:

```text
Undefined array key 0
```

from `_get_extended()` in `config.class.php`.

The causes were:

- incomplete configuration language metadata;
- missing `tab_main` hierarchy;
- numeric `$LANG_tab['monitor'][0]` instead of symbolic `tab_main`;
- `selectionArray = 0` on text fields with no selection list.

Monitor now includes a compatibility repair for Geeklog 2.2.x which, when necessary, normalizes the active site's persisted Monitor configuration before the configuration UI is built.

Expected repairs include:

```text
create tab_main when missing
emails.selectionArray      -> -1
repository.selectionArray  -> -1
emails.tab                 -> 0
repository.tab             -> 0
```

The repair is idempotent and site-scoped.

### Status

- [x] Existing malformed 1.4.0 configuration repaired successfully on Geeklog 2.2.2.
- [x] Monitor Configuration loads without the previous PHP warnings.

---

## 5. Interrupted/retry test

The 1.4.0 database migration is designed to be retryable.

Where practical on a disposable test installation:

1. create a condition that makes one schema migration operation fail;
2. run the plugin upgrade;
3. confirm the installed plugin version is not changed to 1.4.0;
4. review `error.log` for the Monitor migration failure message;
5. correct the database problem;
6. run the upgrade again.

Expected result: the second run completes without requiring manual cleanup of already completed migration steps.

### Status

- [ ] Deliberate interrupted/retry test still to be performed.

---

## 6. Dashboard and configuration smoke test

After upgrade, open the Monitor administration page.

Verify:

- Overview loads without PHP warnings/notices;
- PHP version check is shown;
- Geeklog version check is shown;
- data/log path checks are shown;
- disk-space check fails gracefully if unavailable;
- Monitor code and DB versions both show 1.4.0;
- Ban integration is informational when Ban is absent;
- Plugins view is read-only;
- no plugin installation/update is triggered from Monitor;
- Monitor Configuration opens without PHP warnings;
- the Configuration page displays `emails` and `repository` under the main tab/fieldset.

### Runtime validation recorded

- [x] Geeklog 2.1.1 Monitor runtime validated.
- [x] Geeklog 2.2.2 Monitor admin runtime validated.
- [x] Geeklog 2.2.2 Configuration page validated after config compatibility fixes.
- [x] Removed `COM_siteHeader()` / `COM_siteFooter()` usage no longer breaks Geeklog 2.2.2.

---

## 7. Log viewer test

Verify that:

- only files from `$_CONF['path_log']` can be selected;
- a forged path such as `../../config.php` is rejected;
- only a bounded tail of a large log is read;
- HTML/JavaScript-looking log content is displayed as text, not executed;
- clearing a log requires an administrator POST with a valid Geeklog CSRF token;
- a failed token leaves the file unchanged.

The current implementation is designed around these constraints. A full manual adversarial log-viewer test should remain part of final release validation.

---

## 8. Image diagnostic test

Monitor 1.4.0 no longer embeds an image resizing engine.

The image health check is diagnostic only.

Verify that:

- JPEG/JPG, PNG, GIF and WebP files can be inspected;
- symlinks are skipped;
- no image is modified;
- scan work is bounded to the configured implementation limit (currently 2,000 supported image files per dashboard scan);
- large dimensions and large files produce warnings rather than mutations;
- invalid/unreadable files fail gracefully;
- missing/unreadable image directories produce informational output rather than fatal errors.

Monitor should recommend remediation but must not silently resize or convert source images.

---

## 9. Legacy Monitor security behaviour

Monitor 1.4.0 deliberately reduces its historical ban role.

Verify that:

- `REMOTE_ADDR` is used as the default client IP source;
- `HTTP_CLIENT_IP` and `X-Forwarded-For` are not trusted automatically;
- invalid IP addresses are ignored;
- existing explicit `banned` legacy records are still enforced during the transition;
- profile/contact and account-creation signals create/update observations only;
- Monitor does not create new automatic permanent bans;
- requests are never delayed with `sleep(60)`;
- Ban remains optional.

---

## 10. Ban integration smoke test

When the Ban plugin is enabled:

- Monitor must not query or modify Ban database tables directly;
- Monitor should detect the currently exposed Ban helper capability;
- no ban action should occur merely by opening a Monitor page;
- any future enforcement action must remain explicit and pass through `MonitorBanAdapter.php`.

When Ban is absent, Monitor must continue to operate normally.

### Status

- [x] Ban-absent behaviour is part of the normal Monitor path.
- [ ] Dedicated Ban-present runtime test still required.

---

## 11. Scheduled-task test

Run the Geeklog scheduled task containing Monitor.

Verify:

- expired transitional observations are cleaned up;
- diagnostic table checks do not attempt automatic repair;
- errors are written to Geeklog's normal error log;
- the task does not send/delete entire logs automatically;
- the task completes without PHP warnings on supported PHP versions.

Also review the operational cost of running `CHECK TABLE ... FAST` across the active site's table list. This should remain diagnostic, bounded and should not become an expensive routine merely because Monitor is installed.

### Status

- [ ] Full scheduled-task runtime validation still required.

---

## 12. Shared-files / multisite test

When available, use two Geeklog sites sharing the same Monitor plugin files but using separate databases/configurations.

Suggested sequence:

```text
shared Monitor files: 1.4.0
site A DB state: 1.3.1
site B DB state: 1.3.1
```

Upgrade site A only.

Expected intermediate state:

```text
shared Monitor files: 1.4.0
site A DB state: 1.4.0
site B DB state: 1.3.1
```

Confirm site B remains operational and that upgrading site A does not modify site B's database or configuration.

Then upgrade site B and confirm both sites operate normally.

The configuration self-repair must also remain active-site scoped: opening Monitor Configuration on one site must not modify another site's `conf_values` table.

### Status

- [ ] Two-site shared-files runtime test still required.

---

## 13. Distribution archive validation

The repository workflow generates:

```text
dist/monitor_1.4.0_2.1.1.zip
```

Verify that:

- the archive has a top-level `monitor/` directory as expected by the Geeklog plugin installer;
- no filename or directory component begins with `.`;
- required plugin files are present;
- the ZIP passes `unzip -t`;
- the archive can be uploaded through Geeklog's plugin installer;
- the archive committed under `dist/` corresponds to the current source branch.

### Status

- [x] Build workflow passes.
- [x] Archive validation passes.
- [x] Archive committed to `dist/`.
- [x] Workflow artifact also published.

---

## 14. Release gate for stabilization

Before moving the development focus fully to new Monitor features, the current status is:

- [x] PHP 5.6 syntax workflow passes;
- [x] PHP 8.1 syntax workflow passes;
- [x] security/API regression guard passes;
- [x] Monitor operates on Geeklog 2.1.1;
- [x] Monitor admin operates on Geeklog 2.2.2;
- [x] Geeklog 2.2.2 Configuration UI works without the two previously identified configuration warnings;
- [x] native configuration structure follows the official Polls/core pattern;
- [x] malformed early-1.4.0 configuration can self-repair safely on the active Geeklog 2.2.x site;
- [x] distribution archive build succeeds;
- [x] TimThumb and obsolete image-resize implementation are removed;
- [x] legacy automatic permanent-ban expansion is removed;
- [ ] deliberate database migration failure/retry test succeeds;
- [ ] dedicated Ban-present runtime test succeeds;
- [ ] full scheduled-task test succeeds;
- [ ] two-site shared-files test succeeds.

These remaining tests should be treated as release-hardening tasks, not as reasons to reopen already validated Geeklog 2.1.1/2.2.2 configuration compatibility work.
