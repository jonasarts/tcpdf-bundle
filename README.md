tcpdf-bundle
============

Simplified access to the TCPDF PHP class for the Symfony framework, with helpers
for the Swiss QR-bill.

> **Maintenance mode — the successor is
> [jonasarts/tcpdf2-bundle](https://github.com/jonasarts/tcpdf2-bundle).**
>
> This bundle builds on [`tecnickcom/tcpdf`](https://packagist.org/packages/tecnickcom/tcpdf),
> which upstream now describes as *"Deprecated legacy PDF engine for PHP. Use instead
> tecnickcom/tc-lib-pdf."* Active development has moved to
> [`tecnickcom/tc-lib-pdf`](https://github.com/tecnickcom/tc-lib-pdf).
>
> `tcpdf2-bundle` 8.1.0 runs on that modern engine and keeps the same Swiss QR-bill and
> address-box helpers. It is **not** a drop-in replacement: the legacy drawing API
> (`Cell()`, `MultiCell()`, `SetFont()`, `SetXY()`, `Output()`, …) is gone, because the
> new engine has no cursor and positions everything absolutely. See its
> [changelog](https://github.com/jonasarts/tcpdf2-bundle/blob/main/CHANGELOG.md) for the
> full list of replacements.
>
> The PHP namespace is unchanged (`jonasarts\Bundle\TCPDFBundle\`), so the two packages
> conflict and cannot be installed side by side — switch the requirement, do not add it.
>
> This bundle stays on `tecnickcom/tcpdf` 6.x and receives fixes only.

[![Latest Stable Version](https://poser.pugx.org/jonasarts/tcpdf-bundle/v)](https://packagist.org/packages/jonasarts/tcpdf-bundle)
[![Total Downloads](https://poser.pugx.org/jonasarts/tcpdf-bundle/downloads)](https://packagist.org/packages/jonasarts/tcpdf-bundle)
[![License](https://poser.pugx.org/jonasarts/tcpdf-bundle/license)](https://packagist.org/packages/jonasarts/tcpdf-bundle)
[![CI](https://github.com/jonasarts/tcpdf-bundle/actions/workflows/ci.yml/badge.svg)](https://github.com/jonasarts/tcpdf-bundle/actions/workflows/ci.yml)

Requires PHP 8.4 and Symfony `^7.0 || ^8.0`.

Installation
------------

All installation instructions are in the [documentation](docs/index.md).

Documentation
-------------

* [Documentation index](docs/index.md)
* [Layout helpers & DTOs](docs/03-layout-helpers.md)
* [Change log](CHANGELOG.md)

License
-------

This bundle is released under the MIT license. See [LICENSE](LICENSE).

The underlying TCPDF library ([tecnickcom/tcpdf](https://packagist.org/packages/tecnickcom/tcpdf),
[tcpdf.org](https://tcpdf.org)) is licensed under the GNU LGPL v3 and is pulled
in as a Composer dependency.
