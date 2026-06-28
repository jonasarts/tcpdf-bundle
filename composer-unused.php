<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

// symfony/yaml is a real runtime dependency: the bundle imports
// Resources/config/services.yaml via $container->import(), which needs the YAML
// loader. composer-unused cannot see loader-based usage and falsely reports it
// as unused.
return static fn (Configuration $config): Configuration => $config
    ->addNamedFilter(NamedFilter::fromString('symfony/yaml'));
