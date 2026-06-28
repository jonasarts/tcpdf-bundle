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

namespace jonasarts\Bundle\TCPDFBundle\Tests\Integration;

use jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF;
use jonasarts\Bundle\TCPDFBundle\Tests\TestKernel;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Integration test: boots the TestKernel to prove the SF8 AbstractBundle wiring
 * registers the TCPDF service and keeps it publicly fetchable from the container.
 */
class TCPDFBundleTest extends KernelTestCase
{
    #[Override]
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    /**
     * Instantiating the TCPDF service runs the \TCPDF constructor, which
     * registers a global exception handler it never restores. Drop it here so
     * PHPUnit doesn't flag the test as risky for leaking a handler.
     */
    #[Override]
    protected function tearDown(): void
    {
        restore_exception_handler();

        parent::tearDown();
    }

    public function testBundleBoots(): void
    {
        self::bootKernel();

        $this->assertTrue(self::getContainer()->has(TCPDF::class));
    }

    public function testServiceIsInjectable(): void
    {
        self::bootKernel();

        $pdf = self::getContainer()->get(TCPDF::class);

        $this->assertInstanceOf(TCPDF::class, $pdf);
        $this->assertInstanceOf(\TCPDF::class, $pdf);
    }
}
