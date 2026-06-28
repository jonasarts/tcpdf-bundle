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

use jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The createA3/A4/A5/A6 factories must produce pages with the ISO 216 portrait
 * dimensions (mm). We compare against TCPDF's own scaled page geometry.
 */
class PaperFormatTest extends TestCase
{
    /**
     * @param array{0: int, 1: int} $expectedMm [width, height] in millimetres
     */
    #[DataProvider('paperProvider')]
    public function testPaperFactory(string $factory, array $expectedMm): void
    {
        /** @var TCPDF $pdf */
        $pdf = TCPDF::{$factory}();
        $pdf->AddPage();

        $scale = $pdf->getScaleFactor();

        $this->assertSame(round($expectedMm[0] * $scale), round($pdf->getPageWidth(1)));
        $this->assertSame(round($expectedMm[1] * $scale), round($pdf->getPageHeight(1)));
    }

    /**
     * @return iterable<string, array{string, array{0: int, 1: int}}>
     */
    public static function paperProvider(): iterable
    {
        yield 'A3' => ['createA3', [297, 420]];
        yield 'A4' => ['createA4', [210, 297]];
        yield 'A5' => ['createA5', [148, 210]];
        yield 'A6' => ['createA6', [105, 148]];
    }

    public function testFactoriesReturnTcpdfInstances(): void
    {
        $this->assertInstanceOf(TCPDF::class, TCPDF::createA4());
        $this->assertInstanceOf(\TCPDF::class, TCPDF::createA4());
    }
}
