Setting up the bundle
=====================

## Install the bundle

Execute this console command in your project:

``` bash
$ composer require jonasarts/tcpdf-bundle
```

## Enable the bundle

Register the bundle in the bundle file:

```php
// config/bundles.php

return [
    [...]
    jonasarts\Bundle\TCPDFBundle\TCPDFBundle::class => ['all' => true],
    [...]
];

```

Composer enables the bundle for you in config/bundles.php

Optional register the service class:

```yaml
#config/services.yml
jonasarts\Bundle\TCPDFBundle\TCPDF:
    public: true
```

You can now use `jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF` class.

## That's it

Check out the docs for information on how to use the bundle! [Return to the index.](index.md)
