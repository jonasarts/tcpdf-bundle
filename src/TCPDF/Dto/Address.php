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

/**
 * Immutable address value object for the Swiss QR-bill (ESR).
 *
 * One object serves both parties. Use the combined-address fields
 * ($addressLine1/$addressLine2) for mode K, or the structured fields
 * ($street/$buildingNumber/$postalCode/$city) for mode S.
 *
 * Centralises the country-code validation that previously lived inside the
 * render method.
 */
final class Address
{
    public string $countryCode;

    public function __construct(
        public string $name,
        public ?string $addressLine1 = null,
        public ?string $addressLine2 = null,
        public ?string $street = null,
        public ?string $buildingNumber = null,
        public ?string $postalCode = null,
        public ?string $city = null,
        string $countryCode = 'CH',
    ) {
        if (2 !== strlen($countryCode) || !ctype_alpha($countryCode)) {
            throw new InvalidArgumentException(sprintf('Country code "%s" is not ISO-3166 alpha-2', $countryCode));
        }

        $this->countryCode = strtoupper($countryCode);
    }

    /**
     * Combined-address party (mode K): name + up to two free-form address lines.
     */
    public static function combined(string $name, ?string $addressLine1 = null, ?string $addressLine2 = null, string $countryCode = 'CH'): self
    {
        return new self($name, $addressLine1, $addressLine2, null, null, null, null, $countryCode);
    }

    /**
     * Structured-address party (mode S): name + street/building/postal/city.
     */
    public static function structured(string $name, ?string $street, ?string $buildingNumber, ?string $postalCode, ?string $city, string $countryCode = 'CH'): self
    {
        return new self($name, null, null, $street, $buildingNumber, $postalCode, $city, $countryCode);
    }
}
