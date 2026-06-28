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
 * The Header()/Footer() overrides delegate to a registered closure, bound to
 * the PDF instance ($this inside the closure is the document).
 */
class HeaderFooterTest extends TestCase
{
    public function testHeaderClosureIsInvokedBoundToInstance(): void
    {
        $pdf = new TCPDF();

        $captured = null;
        $pdf->registerHeader(function () use (&$captured): void {
            $captured = $this;
        });

        $pdf->Header();

        $this->assertSame($pdf, $captured);
    }

    public function testFooterClosureIsInvokedBoundToInstance(): void
    {
        $pdf = new TCPDF();

        $captured = null;
        $pdf->registerFooter(function () use (&$captured): void {
            $captured = $this;
        });

        $pdf->Footer();

        $this->assertSame($pdf, $captured);
    }

    public function testHeaderWithoutClosureIsNoop(): void
    {
        $pdf = new TCPDF();

        // no closure registered -> must not error
        $pdf->Header();
        $pdf->Footer();

        $this->assertSame(0, $pdf->getNumPages());
    }
}
