# Layout helpers & DTOs

The core value of this bundle is the set of helpers for building mm-precise PDF layouts by
hand on top of TCPDF. This page covers the paper factories, the address-box helpers, the
Swiss QR-bill (ESR) renderer, and the typed DTO API added in 8.0.

## Paper factories & conveniences

`TCPDF` (the service, `jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF`) extends `\TCPDF` and adds:

- `TCPDF::createA3()`, `createA4()`, `createA5()`, `createA6()` — return a portrait
  document in millimetres for the given ISO 216 format.
- `registerHeader(Closure $c)` / `registerFooter(Closure $c)` — the closure is invoked,
  bound to the document instance, from `Header()` / `Footer()`. Inside the closure `$this`
  is the PDF, so you can call any TCPDF method directly.
- `getTextColor(): array` — the current foreground color as `['R'=>…, 'G'=>…, 'B'=>…]`.

```php
$pdf = TCPDF::createA4();
$pdf->registerFooter(function (): void {
    $this->SetY(-15);
    $this->Cell(0, 10, 'Page '.$this->getAliasNumPage(), 0, 0, 'C');
});
$pdf->AddPage();
```

## Geometry: `Rect`

`TCPDF\Types\Rect` is a small mutable struct (`x`, `y`, `width`, `height`) with
`inset($margin)`, `offset($x, $y)`, `copy()`, the static `Rect::A4($portrait)`, and an
iterator that yields `x, y, width, height` in order. `inset`/`offset` mutate and return
`$this` for chaining.

## Address boxes

For window envelopes and Pingen layouts:

- `PDFHelper::addAddressBoxC5()` / `addAddressBoxC5Right()` — C5 window left / right.
- `PDFHelper::addAddressBoxC65()` — C6/5 document pocket, window right.
- `PDFHelper::addAddressBoxC5Left4Pingen()` / `addAddressBoxC5Right4Pingen()` — Pingen
  address area (22/60/85.5/25.5 mm and 118/60/85.5/25.5 mm).

The exact millimetre coordinates are locked by `tests/Unit/AddressBoxTest.php`.

Typed variant (8.0):

```php
use jonasarts\Bundle\TCPDFBundle\TCPDF\PDFHelper;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\AddressBoxOptions;

PDFHelper::addAddressBox($pdf, 'C5', $address, new AddressBoxOptions(
    pp: 'P.P.',
    sender: 'CH-2501 Biel',
    debug: false,
));
```

`AddressBoxOptions` replaces the old `?array $pp` / `bool $debug` parameter pair; the
positional helpers remain available and unchanged.

## Swiss QR-bill (ESR)

The positional renderer (`PDFHelper::addQrCodeEsr()`, and the service-level
`TCPDF::addQrCodeEsrFooter()` / `addQrCodeEsrPage()` with their page-break handling) is
unchanged and remains the rendering core.

Typed DTO API (8.0), a thin adapter over the positional core:

```php
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\Address;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\EsrPayment;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Enum\EsrMode;

$recipient = Address::structured('Robert Schneider AG', 'Rue du Lac', '1268', '2501', 'Biel', 'CH');
$sender    = Address::structured('Pia Rutschmann', 'Grosse Marktgasse', '28', '9400', 'Rorschach', 'CH');

$payment = new EsrPayment(
    mode: EsrMode::MODE_S,
    qrIban: 'CH44 3199 9123 0008 8901 2',
    iban: 'CH58 0079 1123 0008 8901 2',
    amount: 199700,            // centimes -> 1997.00 CHF
    reference: '210000000003139471430009017',
    subject: 'Invoice 2026-0001',
);

$pdf->addEsrPaymentPage($payment, $recipient, $sender);
```

Use `Address::combined($name, $line1, $line2, $cc)` for mode K (free-form address lines)
and `Address::structured(...)` for mode S (street/building/postal/city). The country code
is validated (ISO-3166 alpha-2) by the `Address` constructor.

### Assets

`addQrCodeEsr()` renders two images — the scissors glyph and the Swiss cross — resolved by
default from `src/Resources/assets/images/` (`schere.png`, `ch-kreuz.png`). See the README
in that directory. Override per call with the `$asset_schere` / `$asset_kreuz` parameters
(or `EsrPayment::$assetSchere` / `$assetKreuz`).

## Banking utilities

`TCPDF\Utils\BankingUtils` provides `modulo10()`, `breakStringIntoBlocks()` and
`generateESRReferenceNumber()` for ESR reference handling. Test vectors are in
`tests/Unit/BankingUtilsTest.php`.
