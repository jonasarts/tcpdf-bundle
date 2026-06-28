<?php

declare(strict_types=1);

/*
 * This file is part of the TCPDF bundle package.
 *
 * (c) Jonas Hauser <symfony@jonasarts.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace jonasarts\Bundle\TCPDFBundle\TCPDF;

use InvalidArgumentException;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\Address;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\AddressBoxOptions;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\EsrPayment;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Enum\EsrMode;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Types\Align;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Types\Rect;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Types\VAlign;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Utils\BankingUtils;

abstract class PDFHelper
{
    final public const FONT_LIGHT = 'helvetica';

    final public const FONT_REGULAR = 'helvetica';

    final public const FONT_MEDIUM = 'helvetica';

    final public const FONT_BOLD = 'helveticaB';

    public static function addDebugGrid(TCPDF &$pdf): void
    {
        // debug grid
        $rect = new Rect(0, 0, 10, 10);

        while ($rect->x < $pdf->getPageWidth()) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);

            $rect->x += $rect->width;
        }

        // reset x
        $rect->x = 0;

        while ($rect->y < $pdf->getPageHeight()) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);

            $rect->y += $rect->height;
        }
    }

    public static function addDefaultFonts(TCPDF $pdf): void
    {
        // AddFont( $family, $style = '', $fontfile = '', $subset = 'default' )

        $pdf->AddFont(self::FONT_LIGHT, '');
        $pdf->AddFont(self::FONT_REGULAR, '');
        $pdf->AddFont(self::FONT_BOLD, 'B');
    }

    /**
     * Address box for letters (with CompanyHeader).
     *
     * for C5 couvert with window left
     *
     * @param array{pp: string, sender: string}|null $pp
     */
    public static function addAddressBoxC5(TCPDF $pdf, string $address, ?array $pp = null, bool $debug = false): void
    {
        // c5 couvert with window left
        // w x h: 10cm x 4.5cm
        // pos left: left / top: 2cm / 4.5cm
        // pos right: -
        // padding: 0.5cm

        // Define the Dimensions
        $rect = new Rect(20, 45, 100, 45);
        $rectWithPadding = clone $rect;
        $rectWithPadding->inset(5);

        self::fillRectWithAddressBox($pdf, $rectWithPadding, $address, null, $pp);

        if ($debug) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);
        }
    }

    /**
     * Adressbox for C5 Left - shifted for Pingen.
     *
     * https://help.pingen.com/de/vorlagen-und-postanforderungen/layout-anforderungen
     * - Adressbereich (X/Y/W/H) 22/60/85.5/25.5mm
     */
    public static function addAddressBoxC5Left4Pingen(TCPDF $pdf, string $address, bool $debug = false): void
    {
        // c5 couvert with window left

        $x = 22;
        $y = 60;
        $w = 85.5;
        $h = 25.5;
        $p = 0;
        $pl = 0;

        $x += $pl;
        $y += $p;
        $w -= ($p + $pl); // = 85.5
        $h -= (2 * $p); // = 25.5

        $x += $pl;
        $y += $p;
        $w -= ($p + $pl);
        $h -= (2 * $p); // = 25.5

        $pdf->setCellHeightRatio(1.25);

        $pdf->SetFont(self::FONT_LIGHT, '', 11);
        $pdf->MultiCell($w, $h, $address, 0, 'L', false, 1, $x, $y);

        $pdf->setCellHeightRatio(1);

        if ($debug) {
            $pdf->Rect($x, $y, $w, $h);
        }
    }

    /**
     * or C5 couvert with window right.
     *
     * @param array{pp: string, sender: string}|null $pp
     */
    public static function addAddressBoxC5Right(TCPDF $pdf, string $address, ?array $pp = null, bool $debug = false): void
    {
        // c5 couvert with window right
        // ... w x h: 10cm x 4.5cm
        // pos left: left / top: 10cm / 4.5cm
        // pos right: - (1cm)
        // padding: 0.5cm
        // padding-left: 2cm
        $rect = new Rect(100, 45, 100, 45);

        $padding = 5;
        $rectWithPadding = clone $rect;
        $rectWithPadding->inset($padding);

        self::fillRectWithAddressBox($pdf, $rectWithPadding, $address, null, $pp);

        if ($debug) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);
        }
    }

    /**
     * Adressbox for C5 Right - shifted for Pingen.
     *
     * https://help.pingen.com/de/vorlagen-und-postanforderungen/layout-anforderungen
     * - Adressbereich (X/Y/W/H) 118/60/85.5/25.5mm
     */
    public static function addAddressBoxC5Right4Pingen(TCPDF $pdf, string $address, bool $debug = false): void
    {
        // c5 couvert with window right
        //
        // Adressbereich (X/Y/W/H) 118/60/85.5/25.5mm
        //
        // padding: 0cm
        // padding-left: 0cm

        $x = 118;
        $y = 60;
        $w = 85.5;
        $h = 25.5;
        $p = 0;
        $pl = 0;

        $x += $pl;
        $y += $p;
        $w -= ($p + $pl); // = 85.5
        $h -= (2 * $p); // = 25.5

        $pdf->setCellHeightRatio(1.25);

        $pdf->SetFont(self::FONT_LIGHT, '', 11);
        $pdf->MultiCell($w, $h, $address, 0, 'L', false, 1, $x, $y);

        $pdf->setCellHeightRatio(1);

        if ($debug) {
            $pdf->Rect($x, $y, $w, $h);
        }
    }

    /**
     * Address box for delivery notes (with CompanyLogo).
     *
     * for C6/5 document pocket with window right
     *
     * @param array{pp: string, sender: string}|null $pp
     */
    public static function addAddressBoxC65(TCPDF $pdf, string $address, ?array $pp = null, bool $debug = false): void
    {
        // c65 document pocket with window right
        // w x h: 9.5cm x 4.5cm
        // pos left: -
        // post right: left / top: 10.5cm / 3.5cm
        // padding: 0.5cm

        $rect = new Rect(105, 35, 95, 45);
        $rect->inset(5);

        self::fillRectWithAddressBox($pdf, $rect, $address, null, $pp);

        if ($debug) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);
        }
    }

    /**
     * Address box for labels.
     *
     * @param array{pp: string, sender: string}|null $pp
     */
    private static function fillRectWithAddressBox(TCPDF $pdf, Rect $rect, string $address, ?string $sender = null, ?array $pp = null): void
    {
        $style = ['width' => 0.25, 'color' => [0, 0, 0, 100]];
        $pdf->setCellHeightRatio(1.25);
        $pdf->setCellPadding(0);

        $offset_y = 0;

        if (is_array($pp)) {
            $pdf->Line($rect->x, $rect->y + 5, $rect->x + $rect->width, $rect->y + 5, $style);

            $pdf->SetFont(self::FONT_BOLD, size: 11);
            $pdf->MultiCell(9, 5, $pp['pp'], 0, Align::LEFT->value, false, 0, $rect->x, $rect->y, true, 0, false, true, 5, VAlign::BOTTOM->value);
            $pdf->SetFont(self::FONT_LIGHT, size: 9);
            $pdf->MultiCell($rect->width - 9 - 21, 5, $pp['sender'], 0, Align::LEFT->value, false, 0, $rect->x + 9, $rect->y, true, 0, false, true, 5, VAlign::BOTTOM->value);
            $pdf->MultiCell(21, 5, 'POST CH AG', 0, Align::RIGHT->value, false, 0, $rect->x + $rect->width - 21, $rect->y, true, 0, false, true, 5, VAlign::BOTTOM->value);

            $offset_y += 5 + 3;
        } else {
            $offset_y += 5 + 3;
        }

        // recipient
        $a = explode("\n", $address);

        $txt = array_shift($a);

        $pdf->SetFont(self::FONT_LIGHT, size: 11);
        $pdf->SetTextColor(0, 0, 0, 100);
        $pdf->MultiCell($rect->width, 5, $txt, 0, Align::LEFT->value, false, 1, $rect->x, $rect->y + $offset_y);

        $offset_y += 5;

        $txt = implode("\n", $a);

        $pdf->SetFont(self::FONT_LIGHT, size: 11);
        $pdf->MultiCell($rect->width, $rect->height, $txt, 0, Align::LEFT->value, false, 1, $rect->x, $rect->y + $offset_y);

        // sender
        if (!in_array($sender, [null, '', '0'], true)) {
            $sender_field_height = 5;

            $pdf->SetFont(self::FONT_LIGHT, size: 9);
            $pdf->SetTextColor(0, 0, 0, 80);
            $txt = $sender;
            $pdf->MultiCell($rect->width, $sender_field_height, $txt, 0, Align::LEFT->value, false, 1, $rect->x, $rect->y + $rect->height - $sender_field_height);
            $style = ['width' => 0.1, 'color' => [0, 0, 0, 80]];
            $pdf->Line(
                $rect->x,
                $rect->y + $rect->height - $sender_field_height,
                $rect->x + $rect->width,
                $rect->y + $rect->height - $sender_field_height,
                $style
            );
        }

        $pdf->setCellHeightRatio(1);
    }

    public static function drawCornerRect(TCPDF $pdf, float $x, float $y, int $w, int $h): void
    {
        $dx = 3;
        $dy = 2;

        // tl
        $pdf->Line($x, $y, $x + $dx, $y);
        $pdf->Line($x, $y, $x, $y + $dy);

        // tr
        $pdf->Line($x + $w - $dx, $y, $x + $w, $y);
        $pdf->Line($x + $w, $y, $x + $w, $y + $dy);

        // bl
        $pdf->Line($x, $y + $h, $x + $dx, $y + $h);
        $pdf->Line($x, $y + $h - $dy, $x, $y + $h);

        // br
        $pdf->Line($x + $w - $dx, $y + $h, $x + $w, $y + $h);
        $pdf->Line($x + $w, $y + $h - $dy, $x + $w, $y + $h);
    }

    /**
     * Render a Swiss QR-bill (ESR) payment part + receipt at the bottom of the page.
     *
     * @throws InvalidArgumentException if $mode is not "S"/"K" or country codes are not ISO-3166 alpha-2
     */
    public static function addQrCodeEsr(
        TCPDF $pdf,
        EsrMode|string $mode,
        string $recipientName,
        ?string $recipientAddress1,
        ?string $recipientAddress2,
        ?string $recipientStreet,
        ?string $recipientBuildingNumber,
        ?string $recipientPostalCode,
        ?string $recipientCity,
        string $recipientCountryCode,
        string $senderName,
        ?string $senderAddress1,
        ?string $senderAddress2,
        ?string $senderStreet,
        ?string $senderBuildingNumber,
        ?string $senderPostalCode,
        ?string $senderCity,
        string $senderCountryCode,
        string $qr_iban,
        string $iban,
        ?int $amount,
        ?string $reference,
        ?string $subject,
        ?string $asset_schere = null,
        ?string $asset_kreuz = null,
    ): void {
        // validate mode (S / K)
        if (is_string($mode)) {
            $esrMode = EsrMode::tryFrom($mode);
            if (!$esrMode instanceof EsrMode) {
                throw new InvalidArgumentException(sprintf('Invalid ESR mode "%s", expected "S" or "K"', $mode));
            }

            $mode = $esrMode;
        }

        // validate data
        if (strlen($recipientCountryCode) < 2) {
            throw new InvalidArgumentException('not enough recipient country code data');
        }

        if (strlen($senderCountryCode) < 2) {
            throw new InvalidArgumentException('not enough sender country code data');
        }

        if (strlen($recipientCountryCode) > 2) {
            throw new InvalidArgumentException('recipient country code is not iso_3166_alpha2');
        }

        if (strlen($senderCountryCode) > 2) {
            throw new InvalidArgumentException('sender country code is not iso_3166_alpha2');
        }

        // resolve the bundled assets (caller may override via parameters)
        $asset_image_schere = self::resolveAsset($asset_schere, 'schere.png');
        $asset_image_kreuz = self::resolveAsset($asset_kreuz, 'ch-kreuz.png');

        $font_size = 9; // base font size / receipt is -1 of base size / payment is = base size

        // sender
        $senderName = trim($senderName);

        // amount
        $str_amount = $amount ? sprintf('%0.2f', $amount / 100) : '0.00';

        // reference
        $reference = trim($reference ?? '');
        if ('' !== $reference && '0' !== $reference) {
            $iban_ = $qr_iban; // CH***
            $iban_parts = str_split($qr_iban, 4);
            $iban_formatted = implode(' ', $iban_parts);

            // QR esr reference does no longer need the banking customer identification (no bic)
            $referenceNumber_formatted = BankingUtils::breakStringIntoBlocks($reference, 5, true);
        } else {
            $iban_ = $iban; // CH***
            $iban_parts = str_split($iban, 4);
            $iban_formatted = implode(' ', $iban_parts);

            $referenceNumber_formatted = '';
        }

        // subject
        $subject = trim($subject ?? '');
        $subject = mb_strimwidth($subject, 0, 140 - 3, '...', 'UTF-8');

        // format K
        $recipientName = mb_strimwidth($recipientName, 0, 70 - 3, '...', 'UTF-8');
        $recipientAddress1 = mb_strimwidth($recipientAddress1 ?? '', 0, 70 - 3, '...', 'UTF-8');
        $recipientAddress2 = mb_strimwidth($recipientAddress2 ?? '', 0, 70 - 3, '...', 'UTF-8');

        // format S
        $recipientStreetOnly = mb_strimwidth($recipientStreet ?? '', 0, 70 - 3, '...', 'UTF-8');
        $recipientBuildingNumber = mb_strimwidth($recipientBuildingNumber ?? '', 0, 16 - 3, '...', 'UTF-8');
        $recipientPostalCode = mb_strimwidth($recipientPostalCode ?? '', 0, 16 - 3, '...', 'UTF-8');
        $recipientCity = mb_strimwidth($recipientCity ?? '', 0, 35 - 3, '...', 'UTF-8');

        // format K
        $senderName = mb_strimwidth($senderName, 0, 70 - 3, '...', 'UTF-8');
        $senderAddress1 = mb_strimwidth($senderAddress1 ?? '', 0, 70 - 3, '...', 'UTF-8');
        $senderAddress2 = mb_strimwidth($senderAddress2 ?? '', 0, 70 - 3, '...', 'UTF-8');

        // format S
        $senderStreetOnly = mb_strimwidth($senderStreet ?? '', 0, 70 - 3, '...', 'UTF-8');
        $senderBuildingNumber = mb_strimwidth($senderBuildingNumber ?? '', 0, 16 - 3, '...', 'UTF-8');
        $senderPostalCode = mb_strimwidth($senderPostalCode ?? '', 0, 16 - 3, '...', 'UTF-8');
        $senderCity = mb_strimwidth($senderCity ?? '', 0, 35 - 3, '...', 'UTF-8');

        // build the SPC QR payload (plain-text, byte-exact per QR-bill standard — never HTML-escaped)
        $qr_data = match ($mode) {
            EsrMode::MODE_K => self::getQrDataK(
                $iban_,
                $recipientName,
                $recipientAddress1,
                $recipientAddress2,
                $recipientCountryCode,
                $amount,
                $senderName,
                $senderAddress1,
                $senderAddress2,
                $senderCountryCode,
                $reference,
                $subject
            ),
            EsrMode::MODE_S => static::getQrDataS(
                $iban_,
                $recipientName,
                $recipientStreetOnly,
                $recipientBuildingNumber,
                $recipientPostalCode,
                $recipientCity,
                $recipientCountryCode,
                $amount,
                $senderName,
                $senderStreetOnly,
                $senderBuildingNumber,
                $senderPostalCode,
                $senderCity,
                $senderCountryCode,
                $reference,
                $subject
            ),
        };

        // receipt block (printed HTML — all dynamic values are HTML-escaped)
        $recipt_text_data = '<h1 style="font-size: 6pt; font-weight: bold;">Konto / Zahlbar an</h1>';
        $recipt_text_data .= '<p>'.self::escapeHtml($iban_formatted);
        $recipt_text_data .= "<br>\n".self::escapeHtml($recipientName);
        if (EsrMode::MODE_S === $mode) {
            $recipt_text_data .= "<br>\n".self::escapeHtml(trim($recipientStreetOnly.' '.$recipientBuildingNumber));
            $recipt_text_data .= "<br>\n".self::escapeHtml(trim($recipientPostalCode.' '.$recipientCity));
        } elseif (EsrMode::MODE_K === $mode) {
            if ('' !== $recipientAddress1 && '0' !== $recipientAddress1) {
                $recipt_text_data .= "<br>\n".self::escapeHtml($recipientAddress1);
            }

            if ('' !== $recipientAddress2 && '0' !== $recipientAddress2) {
                $recipt_text_data .= "<br>\n".self::escapeHtml($recipientAddress2);
            }
        }

        $recipt_text_data .= '</p>';
        if ('' !== $reference && '0' !== $reference) {
            $recipt_text_data .= '<h1 style="font-size:6pt; font-weight: bold;">Referenz</h1>';
            $recipt_text_data .= '<p>'.self::escapeHtml($referenceNumber_formatted).'</p>';
        }

        $recipt_text_data .= '<h1 style="font-size:6pt; font-weight: bold;">Zahlbar durch</h1>';
        if ('' !== $senderName && '0' !== $senderName) {
            $recipt_text_data .= '<p>'.self::escapeHtml($senderName);
            if (EsrMode::MODE_S === $mode) {
                $recipt_text_data .= "<br>\n".self::escapeHtml(trim($senderStreetOnly.' '.$senderBuildingNumber));
                $recipt_text_data .= "<br>\n".self::escapeHtml(trim($senderPostalCode.' '.$senderCity));
            } elseif (EsrMode::MODE_K === $mode) {
                if ('' !== $senderAddress1 && '0' !== $senderAddress1) {
                    $recipt_text_data .= "<br>\n".self::escapeHtml($senderAddress1);
                }

                if ('' !== $senderAddress2 && '0' !== $senderAddress2) {
                    $recipt_text_data .= "<br>\n".self::escapeHtml($senderAddress2);
                }
            }

            $recipt_text_data .= '</p>';
        }

        // payment block (printed HTML — all dynamic values are HTML-escaped)
        $payment_text_data = '<h1 style="font-size: 7pt; font-weight: bold;">Konto / Zahlbar an</h1>';
        $payment_text_data .= '<p>'.self::escapeHtml($iban_formatted);
        $payment_text_data .= "<br>\n".self::escapeHtml($recipientName);
        if (EsrMode::MODE_S === $mode) {
            $payment_text_data .= "<br>\n".self::escapeHtml(trim($recipientStreetOnly.' '.$recipientBuildingNumber));
            $payment_text_data .= "<br>\n".self::escapeHtml(trim($recipientPostalCode.' '.$recipientCity));
        } elseif (EsrMode::MODE_K === $mode) {
            if ('' !== $recipientAddress1 && '0' !== $recipientAddress1) {
                $payment_text_data .= "<br>\n".self::escapeHtml($recipientAddress1);
            }

            if ('' !== $recipientAddress2 && '0' !== $recipientAddress2) {
                $payment_text_data .= "<br>\n".self::escapeHtml($recipientAddress2);
            }
        }

        $payment_text_data .= '</p>';
        if ('' !== $reference && '0' !== $reference) {
            $payment_text_data .= '<h1 style="font-size:7pt; font-weight: bold;">Referenz</h1>';
            $payment_text_data .= '<p>'.self::escapeHtml($referenceNumber_formatted).'</p>';
        }

        if ('' !== $subject && '0' !== $subject) {
            $payment_text_data .= '<h1 style="font-size: 7pt; font-weight: bold;">Zusätzliche Informationen</h1>';
            $payment_text_data .= '<p>'.self::escapeHtml($subject).'</p>';
        }

        $payment_text_data .= '<h1 style="font-size: 7pt; font-weight: bold;">Zahlbar durch</h1>';
        if ('' !== $senderName && '0' !== $senderName) {
            $payment_text_data .= '<p>'.self::escapeHtml($senderName);
            if (EsrMode::MODE_S === $mode) {
                $payment_text_data .= "<br>\n".self::escapeHtml(trim($senderStreetOnly.' '.$senderBuildingNumber));
                $payment_text_data .= "<br>\n".self::escapeHtml(trim($senderPostalCode.' '.$senderCity));
            } elseif (EsrMode::MODE_K === $mode) {
                if ('' !== $senderAddress1 && '0' !== $senderAddress1) {
                    $payment_text_data .= "<br>\n".self::escapeHtml($senderAddress1);
                }

                if ('' !== $senderAddress2 && '0' !== $senderAddress2) {
                    $payment_text_data .= "<br>\n".self::escapeHtml($senderAddress2);
                }
            }

            $payment_text_data .= '</p>';
        }

        $tagvs = ['h1' => [0 => ['h' => 0.5, 'n' => 1], 1 => []], 'p' => [0 => ['h' => 0, 'n' => 0], 1 => ['h' => 0, 'n' => 0]]];
        $pdf->setHtmlVSpace($tagvs);

        // generate esr - swiss qr code einzahlungsschein

        // swiss qr code
        $a4_h = 297;
        $a4_w = 210;
        $r_h = 105;
        $r_w = 62; // 148

        $p = 5; // 5mm padding

        $marginTop = $a4_h - $r_h;

        $leftBorder = 7; // 5+2
        $rightBorder = 7; // 5+2

        $x = $leftBorder;
        $y = $marginTop;

        // EZS
        $pdf->Line(0, $y, $a4_w, $y);
        $pdf->Line($r_w, $y, $r_w, $a4_h);

        // Schere
        $pdf->setFontSubsetting(true);
        $pdf->Image($asset_image_schere, $x, $y - 5, 5);

        // -- Empfangsschein -- -- --

        // - Titel
        $y += $p;
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetXY($x, $y);
        $pdf->Cell($r_w - $p - $leftBorder, 4, 'Empfangsschein');
        $pdf->SetFont('Helvetica', size: $font_size - 1);

        // - Angaben
        $y += 5;
        $pdf->SetXY($x, $y);
        $pdf->writeHTMLCell(
            $r_w - $p - $leftBorder,
            46 + 5 + 5,
            $x,
            $y,
            $recipt_text_data,
            ln: 0
        );

        // empty senderName -> 52x20mm rect
        if ('' === $senderName || '0' === $senderName) {
            $fakeHeight = self::measureHtmlCellHeight($pdf, $r_w - $p - $leftBorder, $payment_text_data);

            $pdf->SetXY($x, $y);
            $pdf->SetLineStyle(['width' => 0.27]);
            static::drawCornerRect($pdf, $x - 2, $y + $fakeHeight, 52, 20);
            $pdf->SetLineStyle(['width' => 0.25]);
        }

        // - Betrag
        $y += 46 + 5 + 5;
        $pdf->SetFont('Helvetica', 'B', size: 7 - 1);
        $pdf->SetXY($x, $y);
        $pdf->Cell($r_w - $p - $leftBorder, 4, 'Währung');
        $pdf->SetXY($r_w - $p - 30, $y);
        $pdf->Cell($r_w - $p - $leftBorder, 4, 'Betrag');
        $pdf->SetFont('Helvetica', size: $font_size - 1);
        $pdf->SetXY($x, $y + 5);
        $pdf->Cell(10, 4, 'CHF');
        if ($amount > 0) {
            $pdf->Text($r_w - $p - 30, $y + 5, $str_amount);
        } else {
            // 30x10mm rect
            $pdf->SetLineStyle(['width' => 0.27]);
            static::drawCornerRect($pdf, $r_w - $p - 30, $y + 4, 30, 10);
            $pdf->SetLineStyle(['width' => 0.25]);

            // - Annahmestelle
            $y += 15;
            $pdf->SetFont('Helvetica', 'B', size: 7 - 1);
            $pdf->SetXY($x, $y);
            $pdf->Cell($r_w - $p - $leftBorder, 19, 'Annahmestelle  ', 0, 0, 'R', false, '', 0, false, 'T', 'T');
        }

        $x = $r_w + $p;
        $y = $marginTop;

        // -- Zahlteil -- -- --

        // - Titel
        $y += $p;
        $pdf->SetFont('Helvetica', 'B', size: 11);
        $pdf->SetXY($x, $y);
        $pdf->Cell($a4_w - $r_w, 4, 'Zahlteil');
        $pdf->SetFont('Helvetica', size: $font_size);

        // - QR Code - 46x46mm
        $y += 5 + $p;

        // QR Code Style
        $style = ['border' => false, 'padding' => 0, 'fgcolor' => [0, 0, 0, 100], 'bgcolor' => false];

        // QRCODE,<quality>
        // quality -> L M Q H : low medium q? high error correction
        $pdf->write2DBarcode($qr_data, 'QRCODE,M', $x, $y, 46, 46, $style, 'N');
        // SWISS QR
        $pdf->Image($asset_image_kreuz, $x + 23 - 3.5, $y + 23 - 3.5, 7, 7);

        // - Betrag
        $y += 46 + $p;
        $pdf->SetFont('Helvetica', 'B', size: 7);
        $pdf->SetXY($x, $y);
        $pdf->Cell(46 + $p, 4, 'Währung       Betrag');
        $pdf->SetFont('Helvetica', size: $font_size);
        $pdf->SetXY($x, $y + 5);
        $pdf->Cell(10, 4, 'CHF');
        if ($amount > 0) {
            // output amount
            $pdf->Text($x + $p + 11, $y + 5, $str_amount);
        } else {
            // 40x15mm rect
            $pdf->SetLineStyle(['width' => 0.27]);
            static::drawCornerRect($pdf, $x + 46 + $p - 40, $y + 4, 40, 15);
            $pdf->SetLineStyle(['width' => 0.25]);
        }

        // - Weitere Informationen
        $y += 15;
        // nothing yet

        // - Angaben - reset y
        $y = $marginTop + $p;
        $pdf->SetFont('Helvetica', size: $font_size);
        $pdf->SetXY($x + 46 + $p, $y);
        $pdf->writeHTMLCell(
            w: $a4_w - $r_w - $p - 46 - $p - $rightBorder,
            h: $r_h - 2 * $p - 19,
            x: $r_w + $p + 46 + $p,
            y: $y,
            html: $payment_text_data,
            ln: 0,
            autopadding: true
        );

        // empty senderName -> 65x25mm rect
        if ('' === $senderName || '0' === $senderName) {
            $fakeHeight = self::measureHtmlCellHeight($pdf, $a4_w - $r_w - $p - 46 - $p - $rightBorder, $payment_text_data);

            $pdf->SetXY($x, $y);
            $pdf->SetLineStyle(['width' => 0.27]);
            static::drawCornerRect($pdf, $x + 46 + $p, $y + $fakeHeight, 65, 25);
            $pdf->SetLineStyle(['width' => 0.25]);
        }
    }

    /**
     * Typed, DTO-based entry point for the Swiss QR-bill (ESR).
     *
     * Thin adapter over {@see addQrCodeEsr()} — the positional method remains the
     * single rendering core, this just unpacks the value objects. The country
     * codes are already validated by the {@see Address} constructor.
     *
     * @throws InvalidArgumentException if $payment->mode and the address data are inconsistent
     */
    public static function addEsrPayment(TCPDF $pdf, EsrPayment $payment, Address $recipient, Address $sender): void
    {
        static::addQrCodeEsr(
            $pdf,
            $payment->mode,
            $recipient->name,
            $recipient->addressLine1,
            $recipient->addressLine2,
            $recipient->street,
            $recipient->buildingNumber,
            $recipient->postalCode,
            $recipient->city,
            $recipient->countryCode,
            $sender->name,
            $sender->addressLine1,
            $sender->addressLine2,
            $sender->street,
            $sender->buildingNumber,
            $sender->postalCode,
            $sender->city,
            $sender->countryCode,
            $payment->qrIban,
            $payment->iban,
            $payment->amount,
            $payment->reference,
            $payment->subject,
            $payment->assetSchere,
            $payment->assetKreuz,
        );
    }

    /**
     * Typed, DTO-based address box. Thin adapter over the C5/C6.5 helpers; the
     * positional helpers remain the rendering core.
     */
    public static function addAddressBox(TCPDF $pdf, string $format, string $address, ?AddressBoxOptions $options = null): void
    {
        $options ??= new AddressBoxOptions();
        $pp = $options->toLegacyArray();
        $debug = $options->debug;

        match ($format) {
            'C5' => static::addAddressBoxC5($pdf, $address, $pp, $debug),
            'C5Right' => static::addAddressBoxC5Right($pdf, $address, $pp, $debug),
            'C65' => static::addAddressBoxC65($pdf, $address, $pp, $debug),
            'C5Left4Pingen' => static::addAddressBoxC5Left4Pingen($pdf, $address, $debug),
            'C5Right4Pingen' => static::addAddressBoxC5Right4Pingen($pdf, $address, $debug),
            default => throw new InvalidArgumentException(sprintf('Unknown address-box format "%s"', $format)),
        };
    }

    /**
     * Resolve a bundled image asset, allowing the caller to override the path.
     *
     * @throws InvalidArgumentException if the resolved file does not exist
     */
    private static function resolveAsset(?string $override, string $defaultFilename): string
    {
        // src/TCPDF/PDFHelper.php -> src/Resources/assets/images/<file>
        $path = $override ?? \dirname(__DIR__).'/Resources/assets/images/'.$defaultFilename;

        if (!is_file($path)) {
            throw new InvalidArgumentException(sprintf('TCPDF asset not found: "%s"', $path));
        }

        return $path;
    }

    /**
     * Measure the rendered height of an HTML cell without touching the real
     * document: the measurement is performed on a clone (side-effect-free).
     */
    private static function measureHtmlCellHeight(TCPDF $pdf, float $width, string $html): float
    {
        $probe = clone $pdf;
        $probe->AddPage();

        $startY = $probe->GetY();
        $probe->writeHTMLCell($width, 0, null, null, $html, 0, 1, false, true, 'L', true);

        return $probe->GetY() - $startY;
    }

    /**
     * Escape a dynamic value for safe inclusion in TCPDF's printed HTML.
     */
    private static function escapeHtml(?string $value): string
    {
        return htmlspecialchars($value ?? '', \ENT_QUOTES, 'UTF-8');
    }

    private static function getQrDataK(
        string $iban,
        string $recipientName,
        string $recipientAddress1,
        string $recipientAddress2,
        string $recipientCountryCode,
        ?int $amount,
        string $senderName,
        string $senderAddress1,
        string $senderAddress2,
        string $senderCountryCode,
        ?string $reference,
        ?string $subject,
    ): string {
        // The "empty lines" in the QR Data are required by the QR IBAN Standard !!!

        $str_amount = '';
        if ($amount) {
            $str_amount = sprintf('%0.2f', $amount / 100);
        }

        $reference_type = in_array($reference, [null, '', '0'], true) ? 'NON' : 'QRR';

        return <<<EOD
SPC
0200
1
{$iban}
K
{$recipientName}
{$recipientAddress1}
{$recipientAddress2}


{$recipientCountryCode}







{$str_amount}
CHF
K
{$senderName}
{$senderAddress1}
{$senderAddress2}


{$senderCountryCode}
{$reference_type}
{$reference}
{$subject}
EPD



EOD;
    }

    public static function getQrDataS(
        string $iban,
        string $recipientName,
        string $recipientStreet,
        string $recipientBuildingNumber,
        string $recipientPostalCode,
        string $recipientCity,
        string $recipientCountryCode,
        ?int $amount,
        string $senderName,
        string $senderStreet,
        string $senderBuildingNumber,
        string $senderPostalCode,
        string $senderCity,
        string $senderCountryCode,
        ?string $reference,
        ?string $subject,
    ): string {
        // The "empty lines" in the QR Data are required by the QR IBAN Standard !!!

        $str_amount = '';
        if ($amount) {
            $str_amount = sprintf('%0.2f', $amount / 100);
        }

        $reference_type = in_array($reference, [null, '', '0'], true) ? 'NON' : 'QRR';

        return <<<EOD
SPC
0200
1
{$iban}
S
{$recipientName}
{$recipientStreet}
{$recipientBuildingNumber}
{$recipientPostalCode}
{$recipientCity}
{$recipientCountryCode}







{$str_amount}
CHF
S
{$senderName}
{$senderStreet}
{$senderBuildingNumber}
{$senderPostalCode}
{$senderCity}
{$senderCountryCode}
{$reference_type}
{$reference}
{$subject}
EPD



EOD;
    }
}
