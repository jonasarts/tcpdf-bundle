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
use jonasarts\Bundle\TCPDFBundle\TCPDF\Utils\BankingUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Fixed test vectors for the Swiss ESR banking helpers.
 *
 * Expected values were computed independently from the recursive modulo-10
 * algorithm and the documented padding rules, then locked here as regression.
 */
class BankingUtilsTest extends TestCase
{
    /**
     * @return iterable<string, array{string, int}>
     */
    public static function modulo10Provider(): iterable
    {
        yield 'zero' => ['0', 0];
        yield 'one' => ['1', 1];
        yield 'digits 1..9' => ['123456789', 4];
        yield 'esr reference 26' => ['210000000003139471430009017', 0];
        yield 'odd length' => ['313947143000901', 8];
    }

    #[DataProvider('modulo10Provider')]
    public function testModulo10(string $number, int $expected): void
    {
        $this->assertSame($expected, BankingUtils::modulo10($number));
    }

    public function testBreakStringIntoBlocksReferenceRightAligned(): void
    {
        // default: 5-char blocks, aligned from the right
        $this->assertSame(
            '21 00000 00003 13947 14300 09017',
            BankingUtils::breakStringIntoBlocks('210000000003139471430009017'),
        );
    }

    public function testBreakStringIntoBlocksIbanLeftAligned(): void
    {
        // IBAN: 4-char blocks, aligned from the left
        $this->assertSame(
            'CH44 3199 9123 0008 8901 2',
            BankingUtils::breakStringIntoBlocks('CH4431999123000889012', 4, false),
        );
    }

    public function testGenerateEsrReferenceWithoutCustomerIdentification(): void
    {
        $result = BankingUtils::generateESRReferenceNumber(null, '3139471430009017');

        $this->assertSame('000000000031394714300090175', $result);
        $this->assertSame(27, strlen($result));
    }

    public function testGenerateEsrReferenceWithCustomerIdentification(): void
    {
        $result = BankingUtils::generateESRReferenceNumber('123456', '12345');

        $this->assertSame('123456000000000000000123456', $result);
        $this->assertSame(27, strlen($result));
    }

    public function testGenerateEsrReferenceEmptyReference(): void
    {
        $result = BankingUtils::generateESRReferenceNumber(null, '');

        $this->assertSame('000000000000000000000000000', $result);
        $this->assertSame(27, strlen($result));
    }

    public function testGenerateEsrReferenceRejectsTooLongCustomerIdentification(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BankingUtils::generateESRReferenceNumber('1234567', '12345');
    }

    public function testGenerateEsrReferenceRejectsTooShortCustomerIdentification(): void
    {
        $this->expectException(InvalidArgumentException::class);

        BankingUtils::generateESRReferenceNumber('12345', '12345');
    }

    public function testGenerateEsrReferenceRejectsOversizeReferenceWithoutCustomer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // 27 digits > 26
        BankingUtils::generateESRReferenceNumber(null, '123456789012345678901234567');
    }

    public function testGenerateEsrReferenceRejectsOversizeReferenceWithCustomer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // 21 digits > (26 - 6)
        BankingUtils::generateESRReferenceNumber('123456', '123456789012345678901');
    }
}
