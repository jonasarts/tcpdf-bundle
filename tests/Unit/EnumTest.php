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

use jonasarts\Bundle\TCPDFBundle\TCPDF\Enum\EsrMode;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Types\Align;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Types\FontStyle;
use jonasarts\Bundle\TCPDFBundle\TCPDF\Types\VAlign;
use PHPUnit\Framework\TestCase;

/**
 * Lock the backed-enum values — they map directly onto TCPDF's string API and
 * the SPC payload header, so any drift is a breaking change.
 */
class EnumTest extends TestCase
{
    public function testEsrModeValues(): void
    {
        $this->assertSame('S', EsrMode::MODE_S->value);
        $this->assertSame('K', EsrMode::MODE_K->value);
        $this->assertSame(EsrMode::MODE_S, EsrMode::from('S'));
        $this->assertNull(EsrMode::tryFrom('X'));
    }

    public function testAlignValues(): void
    {
        $this->assertSame('L', Align::LEFT->value);
        $this->assertSame('C', Align::CENTER->value);
        $this->assertSame('R', Align::RIGHT->value);
        $this->assertSame('J', Align::JUSTIFY->value);
    }

    public function testVAlignValues(): void
    {
        $this->assertSame('T', VAlign::TOP->value);
        $this->assertSame('M', VAlign::MIDDLE->value);
        $this->assertSame('B', VAlign::BOTTOM->value);
    }

    public function testFontStyleValues(): void
    {
        $this->assertSame('', FontStyle::NORMAL->value);
        $this->assertSame('B', FontStyle::BOLD->value);
        $this->assertSame('I', FontStyle::ITALIC->value);
        $this->assertSame('U', FontStyle::UNDERLINE->value);
        $this->assertSame('BIU', FontStyle::BOLD_ITALIC_UNDERLINE->value);
    }
}
