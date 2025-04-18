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
        //AddFont( $family, $style = '', $fontfile = '', $subset = 'default' )

        $pdf->AddFont(static::FONT_LIGHT, $style = '');
        $pdf->AddFont(static::FONT_REGULAR, $style = '');
        //$pdf->AddFont(static::FONT_MEDIUM, $style = '');
        $pdf->AddFont(static::FONT_BOLD, $style = 'B');

    }

    /**
     * Address box for letters (with CompanyHeader)
     *
     * for C5 couvert with window left
     *
     * @param array|null $pp
     */
    public static function addAddressBoxC5(TCPDF $pdf, string $address, array $pp = null, bool $debug = false): void
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

        static::fillRectWithAddressBox($pdf, $rectWithPadding, $address, null, $pp);

        if ($debug) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);
            //$pdf->Rect($rectWithPadding->x, $rectWithPadding->y, $rectWithPadding->width, $rectWithPadding->height);
        }
    }

    /**
     * or C5 couvert with window left
     */
    public static function addAddressBoxC5Right(TCPDF $pdf, string $address, array $pp = null, bool $debug = false): void
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

        /*
        $padding_left = 20;
        $rectWithPadding->x += $padding_left - $padding;
        $rectWithPadding->width -= $padding_left - $padding;
        */

        static::fillRectWithAddressBox($pdf, $rectWithPadding, $address, null, $pp);

        if ($debug) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);
            //$pdf->Rect($rectWithPadding->x, $rectWithPadding->y, $rectWithPadding->width, $rectWithPadding->height);
        }
    }

    /**
     * Adressbox for C5 Right - shifted for Pingen
     *
     * - Adressbereich (X/Y/W/H) 118/60/85.5/25.5mm
     *
     * @param TCPDF $pdf
     * @param string $address
     * @param array|null $pp
     * @return void
     */
    public static function addAddressBoxC5Right4Pingen(TCPDF $pdf, string $address, array $pp = null): void
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

        //$pdf->Rect($x, $y, $w, $h);

        $x += $pl;
        $y += $p;
        $w -= ($p + $pl);
        $h -= (2 * $p); // = 25.5

        $pdf->setCellHeightRatio(1.25);

        if (is_array($pp)) {

            //$pdf->Rect($x, $y, $w, 5);
            $pdf->Line($x, $y+5, $x+$w, $y+5);

            $pdf->SetFont(static::FONT_BOLD, '', 12);
            $pdf->MultiCell($w, 5, $pp['pp'], 0, 'L', false, 1, $x, $y);
            $pdf->SetFont(static::FONT_LIGHT, '', 9);
            // MultiCell($w, $h, $txt, $border=0, $align='J', $fill=0, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0)
            $pdf->MultiCell($w-9, 5, $pp['sender'], 0, 'L', false, 1, $x+9, $y, true, 0, false, true, 5, 'B');
            $pdf->MultiCell($w, 5, "POST CH AG", 0, 'R', false, 1, $x, $y, true, 0, false, true, 5, 'B');

            $y += 5+3;
        }

        $pdf->SetFont(static::FONT_LIGHT, '', 11);
        $pdf->MultiCell($w, 35, $address, 0, 'L', false, 1, $x, $y);

        $pdf->setCellHeightRatio(1);
    }

    /**
     * Address box for delivery notes (with CompanyLogo)
     *
     * for C6/5 document pocket with window right
     */
    public static function addAddressBoxC65(TCPDF $pdf, string $address, array $pp = null, bool $debug = false): void
    {

        // c65 document pocket with window right
        // w x h: 9.5cm x 4.5cm
        // pos left: -
        // post right: left / top: 10.5cm / 3.5cm
        // padding: 0.5cm

        $rect = new Rect(105, 35, 95, 45);
        $rect->inset(5);

        static::fillRectWithAddressBox($pdf, $rect, $address, null, $pp);

        if ($debug) {
            $pdf->Rect($rect->x, $rect->y, $rect->width, $rect->height);
        }
    }

    /**
     * Address box for labels
     */
    private static function fillRectWithAddressBox(TCPDF $pdf, Rect $rect, string $address, string $sender = null, array $pp = null): void
    {
        $style = ['width' => 0.25, 'color' => [0, 0, 0, 100]];
        $pdf->setCellHeightRatio(1.25);
        $pdf->setCellPadding(0);
        //$pdf->Rect(...$rect);

        $offset_y = 0;

        if (is_array($pp)) {
            //$pdf->Rect($x, $y, $w, 5);
            $pdf->Line($rect->x, $rect->y + 5, $rect->x + $rect->width, $rect->y + 5, $style);

            $pdf->SetFont(static::FONT_BOLD, size: 11);
            $pdf->MultiCell(9, 5, $pp['pp'], 0, Align::LEFT->value, false, 0, $rect->x, $rect->y, true, 0, false, true, 5, VAlign::BOTTOM->value);
            $pdf->SetFont(static::FONT_LIGHT, size: 9);
            $pdf->MultiCell($rect->width - 9 - 21, 5, $pp['sender'], 0,  Align::LEFT->value, false, 0, $rect->x + 9, $rect->y, true, 0, false, true, 5, VAlign::BOTTOM->value);
            $pdf->MultiCell(21, 5, "POST CH AG", 0,  Align::RIGHT->value, false, 0, $rect->x + $rect->width - 21, $rect->y, true, 0, false, true, 5, VAlign::BOTTOM->value);

            $offset_y += 5 + 3;
        } else {
            //$pdf->Line($rect->x, $rect->y + 5, $rect->x + $rect->width, $rect->y + 5, $style);

            $offset_y += 5 + 3;
        }

        // recipient
        $a = explode("\n", $address);

        $txt = array_shift($a);

        $pdf->SetFont(static::FONT_LIGHT, size: 11);
        $pdf->SetTextColor(0, 0, 0, 100);
        $pdf->MultiCell($rect->width, 5, $txt, 0, Align::LEFT->value, false, 1, $rect->x, $rect->y + $offset_y);

        $offset_y += 5;

        $txt = implode("\n", $a);

        $pdf->SetFont(static::FONT_LIGHT, size: 11);
        $pdf->MultiCell($rect->width, $rect->height, $txt, 0, Align::LEFT->value, false, 1, $rect->x, $rect->y + $offset_y);


        // sender
        if (!empty($sender)) {
            $sender_field_height = 5;

            $pdf->SetFont(static::FONT_LIGHT, size: 9);
            $pdf->SetTextColor(0, 0, 0, 80);
            $txt = $sender;
            $pdf->MultiCell($rect->width, $sender_field_height, $txt, 0, Align::LEFT->value, false, 1, $rect->x, $rect->y +  $rect->height - $sender_field_height);
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
        ?string $senderAddress,
        ?string $senderStreet,
        ?string $senderBuildingNumber,
        ?string $senderPostalCode,
        ?string $senderCity,
        string $senderCountryCode,
        string $qr_iban,
        string $iban,
        ?int $amount,
        ?string $reference,
        ?string $subject
    ): void
    {
        // validate mode (S / K)
        if (is_string($mode)) {
            if (!in_array($mode, ['S', 'K'])) {
                throw new \RuntimeException();
            }

            $mode = EsrMode::tryFrom($mode);
        }

        // validate data
        if (strlen($recipientCountryCode) < 2) {
            throw new \Exception('not enough recipient country code data');
        }
        if (strlen($senderCountryCode) < 2) {
            throw new \Exception('not enough sender country code data');
        }

        //

        $debug = false;
        $asset_image_schere = __DIR__ . '/../../../assets/images/schere/schere.png';
        $asset_image_kreuz = __DIR__ . '/../../../assets/images/ch-kreuz_7mm/CH-Kreuz_7mm.png';

        $font_size = 9; // base font size / receipt is -1 of base size / payment is = base size

        // sender
        $senderName = trim($senderName);

        // amount
        if (!$amount) {
            $str_amount = "0.00";
        } else {
            $str_amount = sprintf("%0.2f", $amount / 100);
        }

        // reference
        $reference = trim($reference);
        if (!empty($reference)) {
            $iban_ = $qr_iban; // CH***
            $iban_parts = str_split($qr_iban, 4);
            $iban_formatted = join(" ", $iban_parts);

            // QR esr reference does no longer need the banking customer identification (no bic)
            $referenceNumber_formatted = BankingUtils::breakStringIntoBlocks($reference, 5, true);
        } else {
            $iban_ = $iban; // CH***
            $iban_parts = str_split($iban, 4);
            $iban_formatted = join(" ", $iban_parts);

            $referenceNumber_formatted = "";
        }

        // subject
        $subject = trim($subject);

        // format K
        $recipientName = mb_strimwidth($recipientName, 0, 70 - 3, "...", 'UTF-8');
        $recipientAddress1 = mb_strimwidth($recipientAddress1 ?? "", 0, 70-3, "...", 'UTF-8');
        $recipientAddress2 = mb_strimwidth($recipientAddress2 ?? "", 0, 70-3, "...", 'UTF-8');
        //$recipientCountryCode = $recipientCountryCode;

        // +format S
        $recipientStreetOnly = mb_strimwidth($recipientStreet ?? "", 0, 70 - 3, "...", 'UTF-8');
        $recipientBuildingNumber = mb_strimwidth($recipientBuildingNumber ?? "", 0, 16 - 3, "...", 'UTF-8');
        $recipientPostalCode = mb_strimwidth($recipientPostalCode ?? "", 0, 16 - 3, "...", 'UTF-8');
        $recipientCity = mb_strimwidth($recipientCity ?? "", 0, 35 - 3, "...", 'UTF-8');

        // format K
        $senderName = mb_strimwidth($senderName, 0, 70 - 3, "...", 'UTF-8');
        $senderAddress = mb_strimwidth($senderAddress ?? "", 0, 70-3, "...", 'UTF-8');
        //$senderCountryCode = $senderCountryCode;

        // +format S
        $senderStreetOnly = mb_strimwidth($senderStreet ?? "", 0, 70 - 3, "...", 'UTF-8');
        $senderBuildingNumber = mb_strimwidth($senderBuildingNumber ?? "", 0, 16 - 3, "...", 'UTF-8');
        $senderPostalCode = mb_strimwidth($senderPostalCode ?? "", 0, 16 - 3, "...", 'UTF-8');
        $senderCity = mb_strimwidth($senderCity ?? "", 0, 35 - 3, "...", 'UTF-8');

        $subject = mb_strimwidth($subject ?? "", 0, 140 - 3, "...", 'UTF-8');

        //* standard K
        if (EsrMode::MODE_K->value === $mode->value) {
            $qr_data = static::getQrDataK(
                $iban_,
                $recipientName,
                $recipientAddress1,
                $recipientAddress2,
                $recipientCountryCode,
                $amount,
                $senderName,
                $senderAddress,
                $senderCountryCode,
                $reference,
                $subject
            );
        }
        //*/

        //* standard S
        if (EsrMode::MODE_S->value === $mode->value) {
            $qr_data = static::getQrDataS(
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
            );
        }
        //*/

        //*
        //$recipt_text_data = "<div style=\"background-color: red;\">";
        $recipt_text_data = "<h1 style=\"font-size: 6pt; font-weight: bold;\">Konto / Zahlbar an</h1>";
        $recipt_text_data .= "<p>" . $iban_formatted;
        $recipt_text_data .= "<br>\n" . $recipientName;
        if ('S' === $mode) {
            $recipt_text_data .= "<br>\n" . $recipientStreetOnly . " " . $recipientBuildingNumber;
            $recipt_text_data .= "<br>\n" . $recipientPostalCode . " " . $recipientCity;
        } elseif ('K' === $mode) {
            $recipt_text_data .= "<br>\n" . $recipientAddress1;
            $recipt_text_data .= "<br>\n" . $recipientAddress2;
        }
        $recipt_text_data .= "</p>";
        if (!empty($reference)) {
            $recipt_text_data .= "<h1 style=\"font-size:6pt; font-weight: bold;\">Referenz</h1>";
            $recipt_text_data .= "<p>" . $referenceNumber_formatted . "</p>";
        }
        $recipt_text_data .= "<h1 style=\"font-size:6pt; font-weight: bold;\">Zahlbar durch</h1>";
        if (!empty($senderName)) {
            $recipt_text_data .= "<p>" . $senderName;
            if ('S' === $mode) {
                $recipt_text_data .= "<br>\n" . $senderStreetOnly . " " . $senderBuildingNumber;
                $recipt_text_data .= "<br>\n" . $senderPostalCode . " " . $senderCity;
            } elseif ('K' === $mode) {
                $recipt_text_data .= "<br>\n" . $senderAddress;
            }
            // $sender['country']
            $recipt_text_data .= "</p>";
        }
        //$recipt_text_data .= "</div>";
        //*/

        //*
        //$payment_text_data = "<div style=\"background-color: red;\">";
        $payment_text_data = "<h1 style=\"font-size: 7pt; font-weight: bold;\">Konto / Zahlbar an</h1>";
        $payment_text_data .= "<p>" . $iban_formatted;
        $payment_text_data .= "<br>\n" . $recipientName;
        if ('S' === $mode) {
            $payment_text_data .= "<br>\n" . $recipientStreetOnly . " " . $recipientBuildingNumber;
            $payment_text_data .= "<br>\n" . $recipientPostalCode . " " . $recipientCity;
        } elseif ('K' === $mode) {
            $payment_text_data .= "<br>\n" . $recipientAddress1;
            $payment_text_data .= "<br>\n" . $recipientAddress2;
        }
        $payment_text_data .= "</p>";
        if (!empty($reference)) {
            $payment_text_data .= "<h1 style=\"font-size:7pt; font-weight: bold;\">Referenz</h1>";
            $payment_text_data .= "<p>" . $referenceNumber_formatted . "</p>";
        }
        if (!empty($subject)) {
            $payment_text_data .= "<h1 style=\"font-size: 7pt; font-weight: bold;\">Zusätzliche Informationen</h1>";
            $payment_text_data .= "<p>" . $subject . "</p>";
        }
        $payment_text_data .= "<h1 style=\"font-size: 7pt; font-weight: bold;\">Zahlbar durch</h1>";
        if (!empty($senderName)) {
            $payment_text_data .= "<p>" . $senderName;
            if ('S' === $mode) {
                $payment_text_data .= "<br>\n" . $senderStreetOnly . " " . $senderBuildingNumber;
                $payment_text_data .= "<br>\n" . $senderPostalCode . " " . $senderCity;
            } elseif ('K' === $mode) {
                $payment_text_data .= "<br>\n" . $senderAddress;
            }
            // $sender['country']
            $payment_text_data .= "</p>";
        }
        //$payment_text_data .= "</div>";
        //*/

        // prepare page

        // page break ?
        $MIN_HEIGHT_FOR_ESR_FOOTER = 110;
        $remainingHeightOnPage = $pdf->getPageHeight() - $pdf->GetY();

        if ($remainingHeightOnPage < $MIN_HEIGHT_FOR_ESR_FOOTER) {
            $pdf->AddPage();
        }

        // debug only:
        //$recipt_text_data = "Angaben\nA\nB\nC\nD\nE\nF\nG\nH\nI\nJ\nK\nL\nM\nN\nO\nP\nQ\nR\nS\nT\nU\nV\nW\nX\nY\nZ";
        //$payment_text_data = $recipt_text_data;

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
        if ($debug) {
            $pdf->Rect(0, $y + 0, $r_w, $r_h);
            $pdf->Rect($r_w, $y + 0, $a4_w - $r_w, $r_h);
        }
        $pdf->Line(0, $y, $a4_w, $y);
        $pdf->Line($r_w, $y, $r_w, $a4_h);

        // Schere
        $pdf->setFontSubsetting(true);
        $pdf->Image($asset_image_schere, $x, $y - 5, 5);

        // -- Empfangsschein -- -- --

        // - Titel
        $y += $p;
        if ($debug) {
            $pdf->Rect($x, $y, $r_w - $p - $leftBorder, 5);
        }
        $pdf->SetFont('Helvetica', 'B', 11);
        $pdf->SetXY($x, $y);
        $pdf->Cell($r_w - $p - $leftBorder, 4, "Empfangsschein");
        $pdf->SetFont('Helvetica', size: $font_size - 1);

        // - Angaben
        $y += 5;
        if ($debug) {
            $pdf->Rect($x, $y, $r_w - $p - $leftBorder, 46 + 5 + 5);
        }
        $pdf->SetXY($x, $y);
        //$pdf->MultiCell($r_w - $p - $leftBorder, 46 + 5 + 5, $recipt_text_data, 0, 'L', false, 1, '', '', true, 0, false, true, 46 + 5 + 5);
        $pdf->writeHTMLCell(
            $r_w - $p - $leftBorder,
            46 + 5 + 5,
            $x,
            $y,
            $recipt_text_data,
            ln: 0
        );

        // empty senderName -> 52x20mm rect
        if (empty($senderName)) {

            // calculate height - only for empty senderName
            $fake = clone $pdf;
            $fake->AddPage();
            $fakeStartY = $fake->GetY();
            $fake->writeHTMLCell(
                $r_w - $p - $leftBorder,
                0,
                '',
                '',
                $payment_text_data,
                0,
                1,
                false, true, 'L', true);
            $fakeEndY = $fake->GetY();
            $fakeHeight = $fakeEndY - $fakeStartY;

            //debug
            $pdf->Rect($x, $y, $r_w - $p - $leftBorder, $fakeHeight);

            $pdf->SetXY($x, $y);
            $pdf->SetLineStyle(['width' => 0.27]);
            static::drawCornerRect($pdf, $x-2, $y+$fakeHeight, 52, 20);
            $pdf->SetLineStyle(['width' => 0.25]);
        }

        // - Betrag
        $y += 46 + 5 + 5;
        if ($debug) {
            $pdf->Rect($x, $y, $r_w - $p - $leftBorder, 15);
        }
        $pdf->SetFont('Helvetica', 'B', size: 7 - 1);
        $pdf->SetXY($x, $y);
        $pdf->Cell($r_w - $p - $leftBorder, 4, "Währung");
        $pdf->SetXY($r_w - $p - 30, $y);
        $pdf->Cell($r_w - $p - $leftBorder, 4, "Betrag");
        $pdf->SetFont('Helvetica', size: $font_size - 1);
        $pdf->SetXY($x, $y + 5);
        $pdf->Cell(10, 4, "CHF");
        if ($amount > 0) {
            $pdf->Text($r_w - $p - 30, $y + 5, $str_amount);
        } else {
            // 30x10mm rect
            $pdf->SetLineStyle(['width' => 0.27]);
            //$pdf->Rect($r_w - $p - 30, $y+4, 30, 10, '', array());
            static::drawCornerRect($pdf, $r_w - $p - 30, $y + 4, 30, 10);
            $pdf->SetLineStyle(['width' => 0.25]);


            // - Annahmestelle
            $y += 15;
            if ($debug) {
                $pdf->Rect($x, $y, $r_w - $p - $leftBorder, 19);
            }
            $pdf->SetFont('Helvetica', 'B', size: 7 - 1);
            $pdf->SetXY($x, $y);
            $pdf->Cell($r_w - $p - $leftBorder, 19, "Annahmestelle  ", 0, 0, 'R', false, '', 0, false, 'T', 'T');
        }

        $x = $r_w + $p;
        $y = $marginTop;

        // -- Zahlteil -- -- --

        // - Titel
        $y += $p;
        if ($debug) {
            $pdf->Rect($x, $y, 46 + $p, 5);
        }
        $pdf->SetFont('Helvetica', 'B', size: 11);
        $pdf->SetXY($x, $y);
        $pdf->Cell($a4_w - $r_w, 4, "Zahlteil");
        $pdf->SetFont('Helvetica', size: $font_size);

        // - QR Code - 46x46mm
        $y += 5 + $p;
        if ($debug) {
            $pdf->Rect($x, $y, 46, 46);
        }

        // QR Code Style
        $style = ['border' => false, 'padding' => 0, 'fgcolor' => [0, 0, 0, 100], 'bgcolor' => false];

        // QRCODE,<quality>
        // quality -> L M Q H : low medium q? high error correction
        if ($debug) {
            dump($qr_data);
        }

        $pdf->write2DBarcode($qr_data, 'QRCODE,M', $x, $y, 46, 46, $style, 'N');
        // SWISS QR
        $pdf->Image($asset_image_kreuz, $x + 23 - 3.5, $y + 23 - 3.5, 7, 7);

        // - Betrag
        $y += 46 + $p;
        if ($debug) {
            $pdf->Rect($x, $y, 46 + $p, 15);
        }
        $pdf->SetFont('Helvetica', 'B', size: 7);
        $pdf->SetXY($x, $y);
        $pdf->Cell(46 + $p, 4, "Währung       Betrag");
        $pdf->SetFont('Helvetica', size: $font_size);
        $pdf->SetXY($x, $y + 5);
        $pdf->Cell(10, 4, "CHF");
        if ($amount > 0) {
            // output amount
            $pdf->Text($x + $p + 11, $y + 5, $str_amount);
        } else {
            // 40x15mm rect
            $pdf->SetLineStyle(['width' => 0.27]);
            //$pdf->Rect($x + 46 + $p - 40, $y + 4, 40, 15, '', array());
            static::drawCornerRect($pdf, $x + 46 + $p - 40, $y + 4, 40, 15);
            $pdf->SetLineStyle(['width' => 0.25]);
        }

        // - Weitere Informationen
        $y += 15;
        if ($debug) {
            $pdf->Rect($x, $y, $a4_w - $r_w - $p - $rightBorder, 19);
        }
        // nothing yet

        // - Angaben - reset y
        $y = $marginTop + $p;
        if ($debug) {
            $pdf->Rect($x + 46 + $p, $y, $a4_w - $r_w - $p - 46 - $p - $rightBorder, $r_h - 2 * $p - 19);
        }
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
        if (empty($senderName)) {

            // calculate height - only for empty senderName
            $fake = clone $pdf;
            $fake->AddPage();
            $fakeStartY = $fake->GetY();
            $fake->writeHTMLCell(
                $a4_w - $r_w - $p - 46 - $p - $rightBorder,
                0,
                '',
                '',
                $payment_text_data,
                0,
                1,
                false, true, 'L', true);
            $fakeEndY = $fake->GetY();
            $fakeHeight = $fakeEndY - $fakeStartY;

            //debug
            $pdf->Rect($r_w + $p + 46 + $p, $y, $a4_w - $r_w - $p - 46 - $p - $rightBorder, $fakeHeight);

            $pdf->SetXY($x, $y);
            $pdf->SetLineStyle(['width' => 0.27]);
            static::drawCornerRect($pdf, $x + 46 + $p, $y+$fakeHeight, 65, 25);
            $pdf->SetLineStyle(['width' => 0.25]);
        }

    }

    private static function getQrDataK(
        string $iban,
        string $recipientName,
        string $recipientAddress1,
        string $recipientAddress2,
        string $recipientCountryCode,
        ?int $amount,
        string $senderName,
        string $senderAddress,
        string $senderCountryCode,
        ?string $reference,
        ?string $subject
    ): string
    {
        // The "empty lines" in the QR Data are required by the QR IBAN Standard !!!

        $str_amount = "";
        if ($amount) {
            $str_amount = sprintf("%0.2f", $amount / 100);
        }

        if (empty($reference)) {
            $reference_type = "NON";
        } else {
            $reference_type = "QRR";
        }

        $qr_data = <<<EOD
SPC
0200
1
$iban
K
{$recipientName}
{$recipientAddress1}
{$recipientAddress2}


{$recipientCountryCode}







$str_amount
CHF
K
{$senderName}
{$senderAddress}


{$senderCountryCode}
$reference_type
$reference
$subject
EPD



EOD;

        return $qr_data;
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
        ?string $subject
    ): string
    {
        // The "empty lines" in the QR Data are required by the QR IBAN Standard !!!

        $str_amount = "";
        if ($amount) {
            $str_amount = sprintf("%0.2f", $amount / 100);
        }

        if (empty($reference)) {
            $reference_type = "NON";
        } else {
            $reference_type = "QRR";
        }

        $qr_data = <<<EOD
SPC
0200
1
$iban
S
{$recipientName}
{$recipientStreet}
{$recipientBuildingNumber}
{$recipientPostalCode}
{$recipientCity}
{$recipientCountryCode}







$str_amount
CHF
S
{$senderName}
{$senderStreet}
{$senderBuildingNumber}
{$senderPostalCode}
{$senderCity}
{$senderCountryCode}
$reference_type
$reference
$subject 
EPD



EOD;

        return $qr_data;
    }

}