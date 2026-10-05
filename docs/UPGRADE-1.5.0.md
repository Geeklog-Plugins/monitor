# Monitor 1.5.0 Upgrade Validation

This document complements the historical 1.4.0 upgrade procedure. Monitor 1.5.0 does not replace the 1.4.0 migration logic; installations older than 1.4.0 still pass through those existing, idempotent migration steps.

## Compatibility target

- Geeklog 2.1.1 through 2.2.2
- PHP 5.6 through 8.1

## Validation checklist

- [ ] Upgrade an existing Monitor 1.4.0 installation to 1.5.0.
- [ ] Disable and reactivate Monitor; confirm table registration works without warnings.
- [ ] Open Geeklog Configuration and verify the three Monitor settings expose contextual tooltips.
- [ ] Verify Plugins > Discover is sorted alphabetically.
- [ ] Verify installed-plugin version checks still distinguish current, update available and installed-version-newer states.
- [ ] Verify manual GitHub refresh bypasses cached metadata.
- [ ] Verify anonymous tag checks use the conservative cache path.
- [ ] Verify authenticated tag checks use the shorter cache path without exposing the token.
- [ ] Confirm `dist/monitor_1.5.0_2.1.1.zip` installs cleanly.
- [ ] Confirm no obsolete 1.4.0 archive is tracked in `dist/`.
- [ ] Run the interrupted-migration/retry validation on a disposable installation before final release sign-off.

## Non-goals

Monitor 1.5.0 does not take ownership of Hub relationship graphs, marketing clusters or provider-specific content models. New diagnostics should remain read-only by default and should not duplicate another plugin's data ownership.
