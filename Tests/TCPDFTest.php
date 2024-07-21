<?php

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
