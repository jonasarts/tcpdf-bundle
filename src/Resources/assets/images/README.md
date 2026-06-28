# Bundled image assets

`PDFHelper::addQrCodeEsr()` renders two fixed images into the Swiss QR-bill and resolves
them, by default, from **this directory** (override per call via the `$asset_schere` /
`$asset_kreuz` parameters). The resolver throws `InvalidArgumentException` if a file is
missing, so both files below must be present.

Required files (exact names):

- `schere.png` — the scissors glyph printed on the cut line (~5 mm wide).
- `ch-kreuz.png` — the Swiss cross placed in the centre of the QR code (7×7 mm).

These were previously referenced from the consuming application via a fragile relative path
(`__DIR__ . '/../../../../../assets/images/...'`). They now live inside the bundle.

Copy them from the source project, e.g.:

```bash
cp /Users/hauser/PhpstormProjects/BigFoot2-Web/assets/images/schere/schere.png \
   src/Resources/assets/images/schere.png
cp /Users/hauser/PhpstormProjects/BigFoot2-Web/assets/images/ch-kreuz_7mm/CH-Kreuz_7mm.png \
   src/Resources/assets/images/ch-kreuz.png
```
