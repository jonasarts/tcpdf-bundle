<?php

declare(strict_types=1);

namespace jonasarts\Bundle\TCPDFBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TCPDFBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
