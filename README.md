# Monitor for Geeklog

Monitor is being modernized as the Geeklog plugin for **site health, diagnostics, operational security monitoring, alerts and actionable recommendations**.

The `monitor-1.4.0` branch is a development branch and should be tested before production deployment.

## Monitor 1.4.0 direction

The plugin follows a simple rule:

> **Observe -> Diagnose -> Alert -> Recommend -> Act only when explicitly requested**

Monitor should be read-only by default. Sensitive changes require an authenticated administrator, the `monitor.admin` permission, an explicit action and CSRF protection.

Current modernization target:

- Geeklog 2.1.1 through 2.2.2
- PHP 5.6 through 8.1

See [ROADMAP.md](ROADMAP.md) for the full modernization plan.

## Current development dashboard

The 1.4.0 administration interface is being reduced to four responsibilities:

- **Overview** — structured health checks for Geeklog, PHP, paths, logs, disk space and Monitor state;
- **Logs** — bounded, escaped log viewing with explicit CSRF-protected clearing;
- **Security** — recent Monitor security observations and Ban integration status;
- **Plugins** — local installed/code version visibility without automatic deployment.

Historical administration tools unrelated to monitoring are being removed from Monitor rather than carried forward indefinitely.

## Monitor and Ban

Monitor is **not intended to duplicate the Geeklog Ban plugin**.

The preferred responsibility split is:

- Monitor detects suspicious conditions, correlates signals, reports health and recommends actions;
- Ban owns persistent allow/deny rules and enforcement when the Ban plugin is available;
- Monitor must continue to work when Ban is not installed;
- Monitor does not directly query or mutate Ban tables.

An isolated adapter in `lib/MonitorBanAdapter.php` is the integration boundary for current/future Ban capabilities.

## Security changes already started in 1.4.0

The development branch removes or changes several historical behaviours:

- bundled TimThumb removed;
- client IP defaults to validated `REMOTE_ADDR` rather than trusting forwarded headers;
- artificial `sleep(60)` request blocking removed;
- automatic permanent Monitor bans are no longer created from CAPTCHA/DokuWiki log scanning or repeated registration/contact attempts;
- legacy explicit `banned` records remain enforceable during migration;
- log email snapshots are bounded and non-destructive;
- historical third-party upgrade telemetry removed;
- plugin installation from the Monitor dashboard removed while the update architecture is redesigned;
- fresh-install legacy security storage uses InnoDB and lookup indexes.

## Development notes

Monitor 1.4.0 is still under active modernization. In particular, the plugin version in `autoinstall.php` remains at the previous persisted version until the 1.4.0 upgrade migrations and compatibility tests are ready.

Before release, the branch must pass the release gates documented in `ROADMAP.md`, including PHP 5.6/PHP 8.1 testing, Geeklog 2.1.1/2.2.2 testing, shared-files multisite transition testing and security review.

## Issues and contributions

Report bugs and feature requests through the repository issue tracker. Changes should remain focused on Monitor's health/diagnostic/security-monitoring role and avoid recreating specialized plugins inside Monitor.
