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

namespace jonasarts\Bundle\TCPDFBundle\Tests\Support;

use jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF;
use Override;

/**
 * Test double that records the geometry of every MultiCell() call instead of
 * rendering it. Lets the address-box helpers be asserted on exact mm
 * coordinates without producing a PDF.
 *
 * The MultiCell signature mirrors TCPDF 6.x exactly so the override stays
 * compatible with the parent declaration.
 */
final class RecordingTCPDF extends TCPDF
{
    /**
     * @var list<array{w: mixed, h: mixed, x: mixed, y: mixed}>
     */
    public array $multiCellCalls = [];

    #[Override]
    public function MultiCell($w, $h, $txt, $border = 0, $align = 'J', $fill = false, $ln = 1, $x = '', $y = '', $reset = true, $stretch = 0, $ishtml = false, $autopadding = true, $maxh = 0, $valign = 'T', $fitcell = false)
    {
        $this->multiCellCalls[] = ['w' => $w, 'h' => $h, 'x' => $x, 'y' => $y];

        return 1;
    }
}
