<?php

declare(strict_types=1);

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Types;

/**
 * 
 */
enum VAlign: string
{
    case TOP = 'T';
    case MIDDLE = 'M';
    case BOTTOM = 'B';
}