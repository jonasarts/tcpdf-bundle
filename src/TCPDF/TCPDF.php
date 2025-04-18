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

//require_once __DIR__ . '/../lib/technickcom/tcpdf/tcpdf.php';

use Closure;

/**
 * TCPDF Service
 */
class TCPDF extends \TCPDF
{
    private ?Closure $header_closure = null;
    private ?Closure $footer_closure = null;

    public function __construct($orientation='P', $unit='mm', $format='A4')
    {
        // construct the TCPDF class
        // __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
        
        //parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        parent::__construct($orientation, $unit, $format, true, 'UTF-8', false);
    }

    #region header & footer

    // Page header
    public function Header()
    {
        if ($this->header_closure instanceof Closure) {
            $this->header_closure->call($this);
        }
    }

    // Page footer
    public function Footer()
    {
        if ($this->footer_closure instanceof Closure) {
            $this->footer_closure->call($this);
        }
    }

    public function registerHeader(Closure $closure): void
    {
        $this->header_closure = $closure;
    }

    public function registerFooter(Closure $closure): void
    {
        $this->footer_closure = $closure;
    }

    #endregion

    /* helpers */

    /**
     * @throws \ReflectionException
     */
    public function getTextColor(): array
    {
        $r = new \ReflectionObject($this);
        $p = $r->getProperty('fgcolor');
        $p->setAccessible(true);

        return $p->getValue($this);
    }

    /* create 'paper' methods */

    public static function createA3($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A3');
    }

    public static function createA4($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A4');
    }

    public static function createA5($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A5');
    }

    public static function createA6($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A6');
    }

    /* access to pdf-helper methods */

    public function addDefaultFonts(): void
    {
        PDFHelper::addDefaultFonts($this);
    }

    public function addAddressBoxC5(string $address, array $pp = null, bool $debug = false): void
    {
        PDFHelper::addAddressBoxC5($this, $address, $pp, $debug);
    }

    public function addAddressBoxC5Left4Pingen(string $address, bool $debug = false): void
    {
        /**
         * Adressbereich (X/Y/W/H) 22/60/85.5/25.5mm
         */
        PDFHelper::addAddressBoxC5Left4Pingen($this, $address, $debug);
    }

    public function addAddressBoxC5Right(string $address, array $pp = null, bool $debug = false): void
    {
        PDFHelper::addAddressBoxC5Right($this, $address, $pp, $debug);
    }

    public function addAddressBoxC5Right4Pingen(string $address, bool $debug = false): void
    {
        /**
         * Adressbereich (X/Y/W/H) 118/60/85.5/25.5mm
         */
        PDFHelper::addAddressBoxC5Right4Pingen($this, $address, $debug);
    }

    public function addAddressBoxC65(string $address, array $pp = null, bool $debug = false): void
    {
        PDFHelper::addAddressBoxC65($this, $address, $pp, $debug);
    }

    public function addQrCodeEsrFooter(
        string $mode,
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
        bool $use_optional_page_break = false
    ): void
    {
        if ($use_optional_page_break) {
            // page break ?
            $MIN_HEIGHT_FOR_ESR_FOOTER = 110;
            $remainingHeightOnPage = $this->getPageHeight() - $this->GetY();

            if ($remainingHeightOnPage < $MIN_HEIGHT_FOR_ESR_FOOTER) {
                $this->AddPage();
            }
        }

        PDFHelper::addQrCodeEsr(
            $this,
            $mode,
            $recipientName,
            $recipientAddress1,
            $recipientAddress2,
            $recipientStreet,
            $recipientBuildingNumber,
            $recipientPostalCode,
            $recipientCity,
            $recipientCountryCode,
            $senderName,
            $senderAddress1,
            $senderAddress2,
            $senderStreet,
            $senderBuildingNumber,
            $senderPostalCode,
            $senderCity,
            $senderCountryCode,
            $qr_iban,
            $iban,
            $amount,
            $reference,
            $subject,
            $asset_schere,
            $asset_kreuz
        );
    }

    public function addQrCodeEsrPage(
        string $mode,
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
        ?string $asset_kreuz = null
    ): void
    {
        $this->AddPage();
        $this->SetAutoPageBreak(0);

        $this->addQrCodeEsrFooter(
            $mode,
            $recipientName,
            $recipientAddress1,
            $recipientAddress2,
            $recipientStreet,
            $recipientBuildingNumber,
            $recipientPostalCode,
            $recipientCity,
            $recipientCountryCode,
            $senderName,
            $senderAddress1,
            $senderAddress2,
            $senderStreet,
            $senderBuildingNumber,
            $senderPostalCode,
            $senderCity,
            $senderCountryCode,
            $qr_iban,
            $iban,
            $amount,
            $reference,
            $subject,
            $asset_schere,
            $asset_kreuz
        );
    }
}
