# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [8.0.1] - 2026-06-28

### Fixed

- CI: the `composer normalize` gate now uses a project-local
  `ergebnis/composer-normalize`; the php-cs-fixer and Rector configs point at the
  lowercase `tests/` directory (case-sensitive on Linux).

## [8.0.0] - 2026-06-28

Modernization for Symfony 8.1 / PHP 8.4.

### Added

- Typed DTO API (additive, backward compatible): `TCPDF\Dto\Address`,
  `TCPDF\Dto\EsrPayment`, `TCPDF\Dto\AddressBoxOptions` value objects.
- `PDFHelper::addEsrPayment()` and `PDFHelper::addAddressBox()`;
  `TCPDF::addEsrPaymentFooter()` and `TCPDF::addEsrPaymentPage()`. These are thin
  adapters over the existing positional methods, which are unchanged.
- Full unit + integration test suite (PHPUnit 12/13, attributes). PHPStan, Rector
  and PHP-CS-Fixer configs + composer scripts; GitHub Actions CI.

### Changed

- Requires PHP 8.4 and Symfony `^7.0 || ^8.0` (`symfony/config`,
  `symfony/dependency-injection`, `symfony/http-kernel`, `symfony/yaml`).
- Bundle migrated to a single-class `AbstractBundle`; the `DependencyInjection/`
  `TCPDFExtension` and empty `Configuration` classes were removed. The config
  alias stays `tcpdf`. The `TCPDF` service remains public/injectable.
- **Exception types:** invalid input (bad ESR mode, non-ISO-3166 country code,
  wrong reference/customer-id sizes) now throws `InvalidArgumentException` instead
  of `RuntimeException`, in `PDFHelper::addQrCodeEsr*` and
  `BankingUtils::generateESRReferenceNumber()`. Update any
  `catch (RuntimeException)`.
- **`getQrDataS` trailing space:** the S-mode payload previously appended a stray
  space after the `subject` line; this was normalized away to match the K-mode
  output and the QR-bill spec (free-text field, no semantic effect).

### Removed

- `dump()` and dead debug code from `PDFHelper::addQrCodeEsr()`; the clone-based
  HTML height measurement is encapsulated in a side-effect-free helper.
- **Stray debug rectangles:** the empty-sender path previously drew two extra
  rectangles into the output unconditionally; these are gone. The intended corner
  marks (`drawCornerRect`) remain.

### Fixed

- QR-bill image assets (`schere.png`, `ch-kreuz.png`) now resolve from inside the
  bundle (`src/Resources/assets/images/`) instead of a fragile `../../../../../`
  path; the resolver throws if an asset is missing. The `$asset_schere` /
  `$asset_kreuz` caller overrides still work.

### Security

- Dynamic values printed into the QR-bill (names, addresses, reference, subject)
  are now HTML-escaped before `writeHTMLCell()`. The SPC QR payload itself is
  unchanged (never HTML-escaped).

## [7.0.2]

- Updates for PHP 8.3 compatibility.
- Code quality improvements in `TCPDF` and `PDFHelper`.

## [7.0.0]

- Requires PHP 8.2.
- Updated for the Symfony 7 branch.

## [6.4.10]

- Added `PDFHelper` methods.
- Renamed `fillWithAddressBox()` to `fillRectWithAddressBox()`.

## [6.4.0]

- Updated to TCPDF 6.7.*.
- Updated for the Symfony 6.4 branch; prepared for future Symfony 7.
- Added some minor tests.

## [6.3.0]

- Requires PHP 8.1.
- Updated for the Symfony 6.3 branch.

## [6.0.4]

- Renamed files from `.yml` to `.yaml`.

## [6.0.0]

- Updated to TCPDF 6.4.*.
- Update for PHP 8.* compatibility.
- Update for Symfony 5.* compatibility.
- Test release for Symfony 6.x (not ready for production).

## [4.0.4]

- Maintenance release, no changes.

## [4.0.1]

- Updated to TCPDF 6.2.17.

## [4.0.0]

- Release for Symfony 4.x.

## [3.0.0]

- Release for PHP 7.0 — same code base as 4.0.4.

## [1.1.0]

- Release for Symfony 3.x.

## [1.0.2]

- Updated to TCPDF 6.2.11.

## [1.0.1]

- Updated to TCPDF 6.0.062.
