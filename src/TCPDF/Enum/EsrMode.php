<?php

declare(strict_types=1);

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Enum;

enum EsrMode: string
{
    case MODE_S = "S";
    case MODE_K = "K";
}