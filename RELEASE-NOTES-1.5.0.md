# Monitor 1.5.0 release notes

Monitor 1.5.0 builds on the 1.4.0 modernization baseline with reliability improvements, clearer configuration help and a more predictable plugin catalog.

## Highlights

- fixes plugin reactivation when Geeklog loads Monitor from a plugin-management scope where the database table prefix is not otherwise imported;
- adds native Geeklog Configuration tooltips for the notification recipients, GitHub repository owner and optional GitHub token settings;
- refreshes GitHub version tags more appropriately: four-hour cache for anonymous API access, one-hour cache when a token is configured, with manual refresh still bypassing the cache;
- sorts Discover plugins alphabetically;
- keeps the distribution directory limited to the current 1.5.0 installable archive;
- makes the distribution workflow tolerate a branch advancing during archive publication without overwriting newer source;
- preserves the read-only-first diagnostic model and the Geeklog 2.1.1–2.2.2 / PHP 5.6–8.1 compatibility target.

## GitHub API behaviour

Monitor continues to use cached GitHub metadata to avoid unnecessary API traffic. Version-tag freshness now depends on authentication state:

- anonymous API access: four-hour tag cache;
- authenticated API access: one-hour tag cache;
- explicit Refresh action: bypasses the normal cache.

Repository and manifest caches keep their longer lifetimes.

## Scope

Monitor remains a diagnostic/service plugin. Relationship ownership, content clusters and marketing mapping belong to Hub rather than Monitor. Planned Monitor diagnostics may report SEO metadata issues, incomplete language packages and orphaned plugin registrations, but should consume provider-owned contracts where available instead of adding third-party SQL coupling.

## Same-version 1.5.0 repair

Some development or early 1.5.0 installations may already have `pi_version = 1.5.0` before the final configuration fixes are deployed. Monitor therefore repairs its configuration metadata idempotently when the native Geeklog Configuration page is opened, without requiring a 1.5.1 version bump. Existing administrator values are preserved; only missing settings or invalid configuration metadata are repaired. Updated language labels and help text come directly from the shipped language files and therefore do not require database migration.

## Upgrade notes

No destructive migration is introduced by 1.5.0. Existing 1.4.0 migration and compatibility repair logic remains in place for older installations.

After upgrading, verify:

1. Monitor opens normally after plugin reactivation.
2. Configuration displays contextual help for Monitor settings.
3. Plugins > Discover is sorted alphabetically.
4. GitHub version metadata refreshes correctly with and without a configured token.
5. `dist/monitor_1.5.0_2.1.1.zip` is the only tracked distribution archive.
