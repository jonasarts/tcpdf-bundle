<?php

declare(strict_types=1);

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Types;

enum Align: string
{
    case LEFT = 'L';
    case CENTER = 'C';
    case RIGHT = 'R';
    case JUSTIFY = 'J';
}