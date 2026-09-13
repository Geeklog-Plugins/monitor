# Monitor 1.4.0 Upgrade Validation

This document defines the validation procedure for the Monitor 1.4.0 stabilization phase.

The objective is to verify the modernization baseline before adding further features.

## Supported modernization target

- Geeklog 2.1.1 through 2.2.2
- PHP 5.6 through 8.1
- MySQL/MariaDB installations supported by the current Monitor schema

The GitHub workflow performs syntax checks on PHP 5.6 and PHP 8.1 and guards against a small set of known unsafe historical patterns. Runtime upgrade testing still needs a real Geeklog installation and database.

---

## 1. Back up before upgrade testing

Before testing an existing Monitor installation, back up:

- the Geeklog database;
- the current Monitor plugin directory;
- the Geeklog log files if they are needed for comparison.

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

## 4. Interrupted/retry test

The 1.4.0 migration is designed to be retryable.

Where practical on a disposable test installation:

1. create a condition that makes one schema migration operation fail;
2. run the plugin upgrade;
3. confirm the installed plugin version is not changed to 1.4.0;
4. review `error.log` for the Monitor migration failure message;
5. correct the database problem;
6. run the upgrade again.

Expected result: the second run completes without requiring manual cleanup of already completed migration steps.

---

## 5. Dashboard smoke test

After upgrade, open the Monitor administration page.

Verify:

- Overview loads without PHP warnings/notices;
- PHP version check is shown;
- Geeklog version check is shown;
- data/log path checks are shown;
- disk-space check fails gracefully if unavailable;
- Monitor code and DB versions both show 1.4.0;
- Ban integration is informational when Ban is absent;
- Ban integration reports availability when Ban is enabled;
- Plugins view is read-only;
- no plugin installation/update is triggered from Monitor.

---

## 6. Log viewer test

Verify that:

- only files from `$_CONF['path_log']` can be selected;
- a forged path such as `../../config.php` is rejected;
- only a bounded tail of a large log is read;
- HTML/JavaScript-looking log content is displayed as text, not executed;
- clearing a log requires an administrator POST with a valid Geeklog CSRF token;
- a failed token leaves the file unchanged.

---

## 7. Legacy Monitor security behaviour

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

## 8. Ban integration smoke test

When the Ban plugin is enabled:

- Monitor must not query or modify Ban database tables directly;
- Monitor should detect the currently exposed Ban helper capability;
- no ban action should occur merely by opening a Monitor page;
- any future enforcement action must remain explicit and pass through `MonitorBanAdapter.php`.

When Ban is absent, Monitor must continue to operate normally.

---

## 9. Scheduled-task test

Run the Geeklog scheduled task containing Monitor.

Verify:

- expired transitional observations are cleaned up;
- diagnostic table checks do not attempt automatic repair;
- errors are written to Geeklog's normal error log;
- the task does not send/delete entire logs automatically;
- the task completes without PHP warnings on supported PHP versions.

---

## 10. Shared-files / multisite test

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

---

## 11. Release gate for stabilization

Before moving the development focus to new Monitor features, all of these should be true:

- [ ] PHP 5.6 syntax workflow passes;
- [ ] PHP 8.1 syntax workflow passes;
- [ ] security regression guard passes;
- [ ] fresh installation succeeds;
- [ ] 1.3.1 -> 1.4.0 upgrade succeeds;
- [ ] retry after a simulated migration failure succeeds;
- [ ] dashboard smoke test succeeds;
- [ ] log viewer/CSRF test succeeds;
- [ ] legacy security transition behaves as documented;
- [ ] Ban absent test succeeds;
- [ ] Ban present test succeeds;
- [ ] scheduled task test succeeds;
- [ ] shared-files test succeeds where a multisite test environment is available.

Once this gate is satisfied, feature work can resume from `ROADMAP.md` without mixing new capabilities into unresolved migration/runtime issues.
