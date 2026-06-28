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

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Dto;

use InvalidArgumentException;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Enum\EsrMode;

/**
 * Immutable payment value object for the Swiss QR-bill (ESR).
 *
 * Replaces the long positional parameter list of the addQrCodeEsr* family with
 * one typed object. The amount is in centimes (e.g. 1997.00 CHF -> 199700).
 */
final class EsrPayment
{
    public function __construct(
        public EsrMode $mode,
        public string $qrIban,
        public string $iban,
        public ?int $amount = null,
        public ?string $reference = null,
        public ?string $subject = null,
        public ?string $assetSchere = null,
        public ?string $assetKreuz = null,
    ) {
        if ('' === trim($qrIban) && '' === trim($iban)) {
            throw new InvalidArgumentException('EsrPayment requires at least one of qrIban / iban');
        }

        if (null !== $amount && $amount < 0) {
            throw new InvalidArgumentException('EsrPayment amount must not be negative');
        }
    }
}
