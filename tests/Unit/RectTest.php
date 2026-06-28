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

use jonasarts\Bundle\TCPDFBundle\TCPDF\Types\Rect;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\TestCase;

class RectTest extends TestCase
{
    public function testDefaults(): void
    {
        $rect = new Rect();

        $this->assertSame(0, $rect->x);
        $this->assertSame(0, $rect->y);
        $this->assertSame(0, $rect->width);
        $this->assertSame(0, $rect->height);
    }

    public function testA4Landscape(): void
    {
        $rect = Rect::A4();

        $this->assertSame(0, $rect->x);
        $this->assertSame(0, $rect->y);
        $this->assertSame(297, $rect->width);
        $this->assertSame(210, $rect->height);
    }

    public function testA4Portrait(): void
    {
        $rect = Rect::A4(true);

        $this->assertSame(210, $rect->width);
        $this->assertSame(297, $rect->height);
    }

    public function testInsetMutatesAndReturnsSelf(): void
    {
        $rect = new Rect(20, 45, 100, 45);

        $returned = $rect->inset(5);

        $this->assertSame($rect, $returned);
        $this->assertSame(25, $rect->x);
        $this->assertSame(50, $rect->y);
        $this->assertSame(90, $rect->width);
        $this->assertSame(35, $rect->height);
    }

    public function testOffsetMutatesAndReturnsSelf(): void
    {
        $rect = new Rect(10, 10, 50, 50);

        $returned = $rect->offset(5, -3);

        $this->assertSame($rect, $returned);
        $this->assertSame(15, $rect->x);
        $this->assertSame(7, $rect->y);
        $this->assertSame(50, $rect->width);
        $this->assertSame(50, $rect->height);
    }

    public function testCopyReturnsEqualButDistinctInstance(): void
    {
        $rect = new Rect(1, 2, 3, 4);

        $copy = $rect->copy();

        $this->assertNotSame($rect, $copy);
        $this->assertEquals($rect->x, $copy->x);
        $this->assertEquals($rect->y, $copy->y);
        $this->assertEquals($rect->width, $copy->width);
        $this->assertEquals($rect->height, $copy->height);

        // mutating the copy must not affect the original
        $copy->inset(1);
        $this->assertSame(1, $rect->x);
    }

    #[IgnoreDeprecations]
    public function testDeprecatedCloneAliasesCopy(): void
    {
        $rect = new Rect(1, 2, 3, 4);

        $clone = $rect->clone();

        $this->assertNotSame($rect, $clone);
        $this->assertEquals(1, $clone->x);
    }

    public function testIteratorOrder(): void
    {
        $rect = new Rect(11, 22, 33, 44);

        $this->assertSame([11, 22, 33, 44], iterator_to_array($rect));
    }
}
