# CLAUDE.md — rules for AI changes to this bundle

This is a jonasarts Symfony bundle. **`registry2-bundle` is the canonical
template**; keep this bundle aligned (checked by `registry2-bundle/tools/drift-lint.sh`).
This file and `CONTRIBUTING.md` are `export-ignore`d.

## Always

- After editing `composer.json`, run `composer normalize` and
  `composer validate --strict`. `composer normalize` owns field order/format.
- Keep `require` minimal and correct: `php`, `ext-ctype`, `ext-mbstring`,
  `tecnickcom/tcpdf` and the `symfony/*` components used at runtime, at
  `^7.0 || ^8.0`. `symfony/yaml` stays in `require` (the bundle imports
  `Resources/config/services.yaml`).
- The QR-bill assets (`src/Resources/assets/images/schere.png`, `ch-kreuz.png`)
  must be committed and must NOT be `export-ignore`d — they ship in the dist and
  the asset resolver throws if they are missing.
- Before claiming done, all gates must pass: `composer validate --strict`,
  `composer normalize --dry-run`, `composer cs-check`, `composer rector-check`,
  `composer phpstan`, `composer test`, `composer test-integration`,
  `composer audit`, plus `composer-require-checker check` and `composer-unused`.

## Never

- Never add a `version` field (Packagist uses the VCS tag).
- Never use `symfony/symfony`; depend on individual components.
- Never commit `composer.lock` (git-ignored; libraries don't ship a lock).
- Never rename the test dir away from lowercase `tests/`, or the config away from
  `phpunit.dist.xml`.
- Never delete user docs. Process docs (`MODERNIZATION.md`, `EXECUTION_PLAN.md`,
  `HANDOFF-*.md`, `handoffs/`, `docs/changes.md`) must not exist here.
- Never weaken a CI gate to make it pass; fix the cause.

## Conventions

- Test suites: `unit` (`tests/Unit`) and `integration` (`tests/Integration`,
  boots a test kernel). `composer test` = unit; `composer test-integration` =
  integration.
- The `TCPDF` service is `public: true` (BC: fetchable by id / injectable).
- `composer-unused.php` ignores `symfony/yaml` (real but undetectable runtime dep).
- Docs: `docs/index.md`, numbered topics (contiguous), `docs/test.md`. Changelog
  is the root `CHANGELOG.md` (Keep a Changelog + SemVer); top section is
  `Unreleased` until tagged.
- New dev/test/doc artifacts must be added to `.gitattributes` as `export-ignore`.

## Drift

When changing a shared convention, update the registry2 template first, then this
bundle, and confirm with `registry2-bundle/tools/drift-lint.sh`.
