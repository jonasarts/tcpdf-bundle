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

use jonasarts\Bundle\TCPDFBundle\TCPDFBundle;
use Override;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Minimal kernel to exercise the bundle in functional (KernelTestCase) tests.
 */
class TestKernel extends Kernel
{
    use MicroKernelTrait;

    #[Override]
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TCPDFBundle(),
        ];
    }

    private function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'secret' => 'tcpdf-test',
            'test' => true,
            'http_method_override' => false,
            'php_errors' => ['log' => true],
        ]);
    }

    #[Override]
    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/tcpdf-bundle/cache/'.$this->environment;
    }

    #[Override]
    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/tcpdf-bundle/logs';
    }
}
