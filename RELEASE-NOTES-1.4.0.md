# Monitor 1.4.0 release notes

Monitor 1.4.0 modernizes the plugin around a narrower role: **site health, diagnostics, operational security monitoring and actionable recommendations**.

## Highlights

- Modernized administration dashboard for Overview, Logs, Security and Plugins.
- Geeklog 2.1.1 through 2.2.2 compatibility target.
- PHP 5.6 through 8.1 compatibility target with CI syntax coverage.
- Removal of bundled TimThumb and obsolete image-resize/update attack surface.
- Safer client-IP handling based on validated `REMOTE_ADDR` by default.
- Removal of artificial request sleeps and historical third-party telemetry.
- Read-only plugin version/update advisor; Monitor no longer deploys plugin code.
- Plugin cards now use a consistent icon/name header and dedicated status row; compatibility is shown only when an update is available.
- Plugin cards use a single icon resolver and no longer duplicate icons or append a second Geeklog/PHP compatibility block.
- Discover cards now show declared Geeklog/PHP requirements and whether the plugin is compatible with the current site before installation.
- Update cards use the target plugin's remote `plugin.json` for Geeklog/PHP requirements, falling back to the repository default branch when historical tags do not expose the manifest.
- Bounded, escaped log viewing and non-destructive log archive analysis.
- Daily log rotation/archive support with private storage.
- Geeklog administrative log archive list with sortable date, log and size columns; archive previews use a dedicated same-tab detail view with an explicit return link.
- Structured health, change, plugin, log and configuration-audit services.
- Content lifecycle observation through Geeklog save/delete notifications without copying content.
- Multisite/shared-files-aware upgrade design with idempotent migrations.

## Agent, Eclipse and Hub interoperability

Monitor now follows the shared capability contract from the Geeklog development memorandum.

Declared capabilities:

- `monitor.health`
- `monitor.diagnostics`
- `monitor.logs.summary`
- `monitor.plugins.status`
- `dashboard.summary`

A new read-only `dashboard.summary` service provides a compact local operational summary for capability-aware dashboards. Remote repository checks are disabled in this summary so rendering stays bounded.

Monitor remains the owner of diagnostic logic. Agent, Eclipse and Hub consume the same provider contract through Geeklog APIs and services; none is a dependency of Monitor.

The root-level `plugin.json` manifest exposes static identity, icon and minimum runtime requirements without executing plugin code.

## Security and privacy

Monitor services require `monitor.admin`, except the configuration audit which preserves its Root-only boundary. Service responses do not expose filesystem paths, raw configuration values, generated SQL or private archive paths.

State-changing administration actions remain explicit, permission checked and CSRF protected.

## Configuration compatibility

Already-installed 1.4.0 development builds that do not yet contain the optional `github_token` configuration row are repaired idempotently when the Geeklog Configuration page is opened. This repair is now independent of the active site language. `MONITOR_GITHUB_TOKEN` remains the higher-priority runtime source when present.

## Upgrade notes

The package declares version **1.4.0** and minimum Geeklog version **2.1.1**. Upgrade logic is sequential and records 1.4.0 only after required migrations succeed.

The Ban-present integration, scheduled-task runtime and two-site shared-files/multisite upgrade paths have been validated. See `docs/UPGRADE-1.4.0.md` and `ROADMAP.md` for the remaining release gates.

## Distribution

The GitHub Actions build creates:

`dist/monitor_1.4.0_2.1.1.zip`

The archive excludes dotfiles and dot-directories for Geeklog 2.2.2 compatibility and includes the static `plugin.json` manifest.
