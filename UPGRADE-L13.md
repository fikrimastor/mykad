# Upgrade to Laravel 13

## Summary

This upgrade bumps all dev dependencies to be compatible with **Laravel 13** and **PHP 8.3**. The package itself (`fikrimastor/mykad`) has no Laravel framework runtime dependency, but the dev tooling (Testbench, Pest, Larastan) must align with the target Laravel version.

---

## Dependency Changes

| Package | Old | New | Reason |
|---|---|---|---|
| `php` | `^8.1` | `^8.3` | Laravel 13 minimum PHP requirement |
| `orchestra/testbench` | `^9.0.0\|\|^8.22.0` | `^11.0` | Testbench versioning mirrors Laravel — v11 = Laravel 13 |
| `pestphp/pest` | `^2.34` | `^4.0` | Pest v4 is the current stable, built on PHPUnit 12, officially supports Laravel 13 |
| `pestphp/pest-plugin-arch` | `^2.7` | `^3.0` | Aligned with Pest v4 plugin ecosystem |
| `pestphp/pest-plugin-laravel` | `^2.3` | `^4.0` | v4.1.0 explicitly targets Laravel ^13.0 |
| `larastan/larastan` | `^2.9` | `^3.0` | v3.x adds full Laravel 13 support (v3.9.3, released Feb 2026) |
| `nunomaduro/collision` | `^8.1.1\|\|^7.10.0` | `^8.8` | Latest v8.x patch; **see note below** |
| `spatie/laravel-package-tools` | `^1.16` | `^1.16` | Already compatible — supports `^10.0\|^11.0\|^12.0\|^13.0` (latest: v1.93.0) |

---

## Breaking Changes to Watch Out For

### PHP 8.3 minimum
- Remove any code using deprecated dynamic properties (`#[AllowDynamicProperties]` or just fix them).
- PHP 8.3 enforces typed class constants — review if any exist.

### Pest v4
- Built on **PHPUnit 12** — some PHPUnit assertion signatures changed.
- The `arch()` API has minor changes; run `pest --init` in a fresh project to compare scaffolding.
- `pest-plugin-browser` is new in v4 — not required, just available.

### Orchestra Testbench v11
- Requires `workbench/` structure. Confirm `workbench/app/` exists (already present in this repo).
- The `package:purge-mykad` command name may need to match your package slug — verify in `composer.json` scripts.

### Larastan v3
- PHPStan level changes — you may see new errors on first run. Address incrementally.
- Config file (`phpstan.neon`) may need `larastanVersion: 3` or updated includes path.

### nunomaduro/collision (⚠️ Caveat)
- **v8.x currently has a conflict** declared against `laravel/framework >=13.0.0` in its own `composer.json`.
- For a **Laravel package** (not a full app), this is generally fine because `laravel/framework` is not a direct dependency — Testbench pulls it in under dev context.
- Monitor [nunomaduro/collision releases](https://github.com/nunomaduro/collision/releases) for a v9.x or a patch that lifts the constraint.
- If `composer update` fails due to this conflict, try removing `collision` from `require-dev` temporarily — Pest v4 ships its own error output handling.

---

## Upgrade Checklist

- [ ] Run `composer update` (this bumps lock file)
- [ ] Fix any immediate Composer conflicts (especially `collision` — see note above)
- [ ] Run `./vendor/bin/pest` — fix any PHPUnit 12 / Pest v4 assertion changes
- [ ] Run `./vendor/bin/phpstan analyse` — fix new Larastan v3 errors
- [ ] Run `./vendor/bin/pint` — ensure code style passes
- [ ] Update GitHub Actions / CI matrix to use PHP 8.3 and drop PHP 8.1/8.2 if desired
- [ ] Update the `README.md` badges and compatibility table to reflect Laravel 13 support
- [ ] Tag a new minor/major release once all checks pass
