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

/**
 * Typed options for the address-box helpers, replacing the untyped
 * "?array $pp / bool $debug" parameter mix.
 *
 * $pp / $sender drive the optional "P.P." franking header row.
 */
final class AddressBoxOptions
{
    public function __construct(
        public ?string $pp = null,
        public ?string $sender = null,
        public bool $debug = false,
    ) {
    }

    public function hasFrankingHeader(): bool
    {
        return null !== $this->pp || null !== $this->sender;
    }

    /**
     * Legacy "pp" array shape consumed by PDFHelper::fillRectWithAddressBox().
     *
     * @return array{pp: string, sender: string}|null
     */
    public function toLegacyArray(): ?array
    {
        if (!$this->hasFrankingHeader()) {
            return null;
        }

        return ['pp' => $this->pp ?? '', 'sender' => $this->sender ?? ''];
    }
}
