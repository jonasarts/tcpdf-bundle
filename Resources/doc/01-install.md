Setting up the bundle
=====================

## Install the bundle

Execute this console command in your project:

``` bash
$ composer require jonasarts/tcpdf-bundle
```

## Enable the bundle

Symfony 2.x - Register the bundle in the kernel:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    // ...

    public function registerBundles()
    {
        $bundles = array(
            // ...
            new jonasarts\Bundle\TCPDFBundle\TCPDFBundle(),
        );

    // ...
    }
}
```

Symfony 4.x - Register the bundle in the bundle file:

```php
// config/bundles.php

return [
    [...]
    jonasarts\Bundle\TCPDFBundle\TCPDFBundle::class => ['all' => true],
    [...]
];

```

## That's it

Check out the docs for information on how to use the bundle! [Return to the index.](index.md)