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

namespace jonasarts\Bundle\TCPDFBundle;

use Override;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * TCPDF Bundle.
 *
 * Single-class bundle (AbstractBundle): there is no separate Extension /
 * Configuration class. The configuration tree and container wiring live here.
 */
class TCPDFBundle extends AbstractBundle
{
    /**
     * Pin the config/extension alias to "tcpdf".
     *
     * Keeps the historical `tcpdf:` configuration key stable regardless of how
     * AbstractBundle would otherwise derive it from the class name.
     */
    protected string $extensionAlias = 'tcpdf';

    /**
     * Keep the package root (next to Resources/ and tests/) as the bundle path.
     */
    #[Override]
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    /**
     * Configuration tree.
     *
     * Currently empty (the bundle exposes no options yet); the empty root node
     * leaves a clean seam for future options without breaking the alias.
     */
    #[Override]
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode();
    }

    /**
     * @param array<string, mixed> $config
     */
    #[Override]
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__.'/Resources/config/services.yaml');
    }
}
