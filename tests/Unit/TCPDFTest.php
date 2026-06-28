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
use PHPUnit\Framework\TestCase;

/**
 * Pure unit tests for the TCPDF service subclass — no Kernel required.
 */
class TCPDFTest extends TestCase
{
    public function testClass(): void
    {
        $pdf = new TCPDF();

        $this->assertInstanceOf(TCPDF::class, $pdf);
        $this->assertInstanceOf(\TCPDF::class, $pdf);
    }

    public function testCall(): void
    {
        $pdf = new TCPDF();

        $this->assertSame(0, $pdf->getNumPages());
    }

    public function testGetTextColor(): void
    {
        $pdf = new TCPDF();

        // default text color should be black
        $color = $pdf->getTextColor();
        $this->assertIsArray($color);
        $this->assertEquals(0, $color['R']);
        $this->assertEquals(0, $color['G']);
        $this->assertEquals(0, $color['B']);

        // change text color and verify
        $pdf->SetTextColor(255, 128, 64);
        $color = $pdf->getTextColor();
        $this->assertEquals(255, $color['R']);
        $this->assertEquals(128, $color['G']);
        $this->assertEquals(64, $color['B']);
    }
}
