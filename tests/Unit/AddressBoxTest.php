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

use jonasarts\Bundle\TCPDFBundle\TCPDF\Dto\AddressBoxOptions;
use jonasarts\Bundle\TCPDFBundle\TCPDF\PDFHelper;
use jonasarts\Bundle\TCPDFBundle\Tests\Support\RecordingTCPDF;
use PHPUnit\Framework\TestCase;

/**
 * Regression on the millimetre coordinates the address-box helpers place text
 * at. These are layout contracts relied on by ~120 products (window-envelope
 * and Pingen positioning), so the exact mm values are locked here.
 */
class AddressBoxTest extends TestCase
{
    private const string ADDRESS = "Robert Schneider AG\nRue du Lac 1268\n2501 Biel";

    public function testC5Left4PingenPositioning(): void
    {
        $pdf = new RecordingTCPDF();

        PDFHelper::addAddressBoxC5Left4Pingen($pdf, self::ADDRESS);

        $this->assertCount(1, $pdf->multiCellCalls);
        $this->assertCellGeometry($pdf->multiCellCalls[0], 85.5, 25.5, 22, 60);
    }

    public function testC5Right4PingenPositioning(): void
    {
        $pdf = new RecordingTCPDF();

        PDFHelper::addAddressBoxC5Right4Pingen($pdf, self::ADDRESS);

        $this->assertCount(1, $pdf->multiCellCalls);
        $this->assertCellGeometry($pdf->multiCellCalls[0], 85.5, 25.5, 118, 60);
    }

    public function testC5Positioning(): void
    {
        $pdf = new RecordingTCPDF();

        PDFHelper::addAddressBoxC5($pdf, self::ADDRESS);

        // rect 20/45/100/45 inset 5 -> 25/50/90/35, recipient offset 8 then 13
        $this->assertCount(2, $pdf->multiCellCalls);
        $this->assertCellGeometry($pdf->multiCellCalls[0], 90, 5, 25, 58);
        $this->assertCellGeometry($pdf->multiCellCalls[1], 90, 35, 25, 63);
    }

    public function testC5RightPositioning(): void
    {
        $pdf = new RecordingTCPDF();

        PDFHelper::addAddressBoxC5Right($pdf, self::ADDRESS);

        // rect 100/45/100/45 inset 5 -> 105/50/90/35
        $this->assertCount(2, $pdf->multiCellCalls);
        $this->assertCellGeometry($pdf->multiCellCalls[0], 90, 5, 105, 58);
        $this->assertCellGeometry($pdf->multiCellCalls[1], 90, 35, 105, 63);
    }

    public function testC65Positioning(): void
    {
        $pdf = new RecordingTCPDF();

        PDFHelper::addAddressBoxC65($pdf, self::ADDRESS);

        // rect 105/35/95/45 inset 5 -> 110/40/85/35
        $this->assertCount(2, $pdf->multiCellCalls);
        $this->assertCellGeometry($pdf->multiCellCalls[0], 85, 5, 110, 48);
        $this->assertCellGeometry($pdf->multiCellCalls[1], 85, 35, 110, 53);
    }

    public function testDtoAddressBoxDelegatesToSameGeometry(): void
    {
        $positional = new RecordingTCPDF();
        PDFHelper::addAddressBoxC5Left4Pingen($positional, self::ADDRESS);

        $dto = new RecordingTCPDF();
        PDFHelper::addAddressBox($dto, 'C5Left4Pingen', self::ADDRESS, new AddressBoxOptions());

        $this->assertEquals($positional->multiCellCalls, $dto->multiCellCalls);
    }

    /**
     * @param array{w: mixed, h: mixed, x: mixed, y: mixed} $call
     */
    private function assertCellGeometry(array $call, float $w, float $h, float $x, float $y): void
    {
        $this->assertEqualsWithDelta($w, $call['w'], 0.001, 'width');
        $this->assertEqualsWithDelta($h, $call['h'], 0.001, 'height');
        $this->assertEqualsWithDelta($x, $call['x'], 0.001, 'x');
        $this->assertEqualsWithDelta($y, $call['y'], 0.001, 'y');
    }
}
