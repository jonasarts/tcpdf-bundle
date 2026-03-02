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

namespace jonasarts\Bundle\TCPDFBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * 
 */
class TCPDFTest extends WebTestCase
{
    public function testClass()
    {
        $pdf = new \jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF();

        $this->assertInstanceOf(\jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF::class, $pdf);
        $this->assertInstanceOf(\TCPDF::class, $pdf);
    }

    public function testCall()
    {
        $pdf = new \jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF();

        $num = $pdf->getNumPages();

        $this->assertTrue($num == 0);
    }
    public function testGetTextColor()
    {
        $pdf = new \jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF();

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

    public function testCreateA4()
    {
        $pdf = new \jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF();

        $a4 = $pdf->createA4();

        $a4->addPage();

        $scale = $a4->getScaleFactor();

        $this->assertEquals(round(210 * $scale), round($a4->getPageWidth(1)));
        $this->assertEquals(round(297 * $scale), round($a4->getPageHeight(1)));
    }
}
