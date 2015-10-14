Setting up the bundle
=====================

## Install the bundle

First add the bundle to your composer.json file: 

```json
{
    // ...
    "require": {
        // ...
        "jonasarts/tcpdf-bundle": "1.0.*"
    },
    "minimum-stability": "stable",
    // ...
}
```

Then run composer.phar:

``` bash
$ php composer.phar install
```

## Enable the bundle

Enable the bundle in the kernel:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new jonasarts\Bundle\TCPDFBundle\TCPDFBundle(),
    );
}
```

## That's it

Check out the docs for information on how to use the bundle! [Return to the index.](index.md)