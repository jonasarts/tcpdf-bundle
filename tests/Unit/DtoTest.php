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

namespace jonasarts\Bundle\TCPDFBundle\Tests\Unit;

use InvalidArgumentException;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\Address;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\AddressBoxOptions;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\EsrPayment;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Enum\EsrMode;
use PHPUnit\Framework\TestCase;

class DtoTest extends TestCase
{
    public function testAddressNormalisesCountryCodeToUppercase(): void
    {
        $address = new Address('Robert Schneider AG', countryCode: 'ch');

        $this->assertSame('CH', $address->countryCode);
    }

    public function testAddressRejectsTooLongCountryCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Address('X', countryCode: 'CHE');
    }

    public function testAddressRejectsNonAlphaCountryCode(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Address('X', countryCode: 'C1');
    }

    public function testAddressCombinedFactory(): void
    {
        $address = Address::combined('Name', 'Rue du Lac 1268', '2501 Biel', 'CH');

        $this->assertSame('Rue du Lac 1268', $address->addressLine1);
        $this->assertSame('2501 Biel', $address->addressLine2);
        $this->assertNull($address->street);
    }

    public function testAddressStructuredFactory(): void
    {
        $address = Address::structured('Name', 'Rue du Lac', '1268', '2501', 'Biel', 'CH');

        $this->assertSame('Rue du Lac', $address->street);
        $this->assertSame('1268', $address->buildingNumber);
        $this->assertNull($address->addressLine1);
    }

    public function testEsrPaymentHoldsValues(): void
    {
        $payment = new EsrPayment(EsrMode::MODE_S, 'CH44...', 'CH58...', 199700, 'REF', 'subject');

        $this->assertSame(EsrMode::MODE_S, $payment->mode);
        $this->assertSame(199700, $payment->amount);
    }

    public function testEsrPaymentRejectsMissingIbans(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EsrPayment(EsrMode::MODE_S, '  ', '');
    }

    public function testEsrPaymentRejectsNegativeAmount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new EsrPayment(EsrMode::MODE_K, 'CH44', 'CH58', -1);
    }

    public function testAddressBoxOptionsLegacyArray(): void
    {
        $options = new AddressBoxOptions('P.P.', 'CH-2501 Biel', true);

        $this->assertTrue($options->hasFrankingHeader());
        $this->assertSame(['pp' => 'P.P.', 'sender' => 'CH-2501 Biel'], $options->toLegacyArray());
        $this->assertTrue($options->debug);
    }

    public function testAddressBoxOptionsEmptyYieldsNullLegacyArray(): void
    {
        $options = new AddressBoxOptions();

        $this->assertFalse($options->hasFrankingHeader());
        $this->assertNull($options->toLegacyArray());
        $this->assertFalse($options->debug);
    }
}
