Testing the bundle
==================

This bundle ships a real test suite plus the usual static-analysis and
code-style tooling. All commands are exposed as Composer scripts, mirroring the
other jonasarts bundles.

## Requirements

- PHP 8.4+
- Composer

No external services are needed.

## Install the dev dependencies

From the bundle root:

```bash
composer install
```

This pulls in PHPUnit, PHPStan, Rector and PHP-CS-Fixer (see `require-dev`).

## Composer scripts

| Command | What it runs |
|---------|--------------|
| `composer test` | PHPUnit – **unit** suite (default suite) |
| `composer test-integration` | PHPUnit – **integration** suite |
| `composer phpstan` | Static analysis (`phpstan.dist.neon`) |
| `composer cs-check` | PHP-CS-Fixer dry-run (report only) |
| `composer cs` | PHP-CS-Fixer – apply fixes |
| `composer rector-check` | Rector dry-run (report only) |
| `composer rector` | Rector – apply changes |

A full local check before tagging:

```bash
composer cs-check
composer rector-check
composer phpstan
composer test
composer test-integration
```

## Test suites

The suites are defined in `phpunit.dist.xml`.

### unit — `tests/Unit/`

Pure unit tests; no Symfony kernel is booted. They cover the PDF helper and the
Swiss QR-bill logic:

- `TCPDFTest` — the `TCPDF` / `PDFHelper` drawing behaviour, exercised via
  `tests/Support/RecordingTCPDF` (a recording double that captures the drawing
  calls).
- `AddressBoxTest` / `HeaderFooterTest` — layout helpers and their millimetre
  coordinates.
- `BankingUtilsTest` — ESR reference-number generation and validation.
- `QrPayloadTest` — the SPC QR payload (K-/S-mode), escaping and edge cases.
- `DtoTest` / `EnumTest` / `RectTest` / `PaperFormatTest` — the typed DTO API,
  enums, geometry and paper-format value objects.

Run only the unit suite:

```bash
composer test
# or
vendor/bin/phpunit --testsuite unit
```

### integration — `tests/Integration/`

`TCPDFBundleTest` boots a minimal `tests/TestKernel.php` (FrameworkBundle + this
bundle) and asserts that the bundle registers and that the `TCPDF` service is
wired and injectable.

Run only the integration suite:

```bash
composer test-integration
# or
vendor/bin/phpunit --testsuite integration
```

The `TestKernel` writes its cache/logs to the system temp directory, so no
project-level `var/` is required.

## Running a single test

```bash
vendor/bin/phpunit --testsuite unit --filter testGeneratesEsrReference
```

## Coverage

Coverage needs Xdebug or PCOV. With one of them enabled:

```bash
XDEBUG_MODE=coverage vendor/bin/phpunit --testsuite unit --coverage-text
```

The covered sources are restricted to `src/` (see the `<source>` block in
`phpunit.dist.xml`).

## Continuous integration

`.github/workflows/ci.yml` runs the whole chain on a PHP 8.4 matrix:
`composer test`, `composer test-integration`, PHPStan, `composer rector-check`
and `composer cs-check`. A green pipeline is the release gate.

[Return to the index.](index.md)
