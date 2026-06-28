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

use jonasarts\Bundle\TCPDFBundle\TCPDF\PDFHelper;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

/**
 * Golden-master tests locking the SPC (Swiss QR-bill) payload byte layout,
 * including the empty lines mandated by the standard. If these fail, the QR
 * content changed — review against the QR-bill spec before updating fixtures.
 */
class QrPayloadTest extends TestCase
{
    public function testGetQrDataSMatchesGolden(): void
    {
        $expected = file_get_contents(__DIR__.'/../fixtures/qr_s_example.txt');

        $actual = PDFHelper::getQrDataS(
            'CH5800791123000889012',
            'Robert Schneider AG',
            'Rue du Lac',
            '1268/2/22',
            '2501',
            'Biel',
            'CH',
            null,
            'Pia-Maria Rutschmann-Schnyder',
            'Grosse Marktgasse',
            '28',
            '9400',
            'Rorschach',
            'CH',
            '',
            '',
        );

        $this->assertSame($expected, $actual);
    }

    public function testGetQrDataKMatchesGolden(): void
    {
        $expected = file_get_contents(__DIR__.'/../fixtures/qr_k_example.txt');

        // getQrDataK is private; exercise it through reflection to lock the layout.
        $method = new ReflectionMethod(PDFHelper::class, 'getQrDataK');
        $actual = $method->invoke(
            null,
            'CH4431999123000889012',
            'Robert Schneider AG',
            'Rue du Lac 1268',
            '2501 Biel',
            'CH',
            199700,
            'Pia-Maria Rutschmann-Schnyder',
            'Grosse Marktgasse 28',
            '9400 Rorschach',
            'CH',
            '210000000003139471430009017',
            'Order from 15.06.2026',
        );

        $this->assertSame($expected, $actual);
    }

    public function testReferenceTypeIsQrrWhenReferencePresent(): void
    {
        $payload = PDFHelper::getQrDataS(
            'CH5800791123000889012', 'R', 'S', '1', '2501', 'Biel', 'CH', 100,
            'Sender', 'St', '2', '9400', 'City', 'CH', '210000000003139471430009017', '',
        );

        $this->assertStringContainsString("\nQRR\n", $payload);
    }

    public function testReferenceTypeIsNonWhenReferenceEmpty(): void
    {
        $payload = PDFHelper::getQrDataS(
            'CH5800791123000889012', 'R', 'S', '1', '2501', 'Biel', 'CH', 100,
            'Sender', 'St', '2', '9400', 'City', 'CH', '', '',
        );

        $this->assertStringContainsString("\nNON\n", $payload);
    }

    public function testPayloadStartsWithSpcHeader(): void
    {
        $payload = PDFHelper::getQrDataS(
            'CH5800791123000889012', 'R', 'S', '1', '2501', 'Biel', 'CH', null,
            'Sender', 'St', '2', '9400', 'City', 'CH', '', '',
        );

        $this->assertStringStartsWith("SPC\n0200\n1\n", $payload);
        $this->assertSame(34, substr_count($payload, "\n") + 1);
    }

    /**
     * Security regression: dynamic values printed via writeHTMLCell() must be
     * HTML-escaped so master data cannot inject TCPDF markup.
     */
    public function testEscapeHtmlEscapesSpecialCharacters(): void
    {
        $method = new ReflectionMethod(PDFHelper::class, 'escapeHtml');

        $this->assertSame(
            '&lt;b&gt;A&amp;B&quot;&#039;&lt;/b&gt;',
            $method->invoke(null, '<b>A&B"\'</b>'),
        );
    }

    public function testEscapeHtmlHandlesNull(): void
    {
        $method = new ReflectionMethod(PDFHelper::class, 'escapeHtml');

        $this->assertSame('', $method->invoke(null, null));
    }
}
