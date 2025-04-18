<?php

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Types;

enum FontStyle: string
{
    case NORMAL = '';
    case BOLD = 'B';
    case ITALIC = 'I';
    case UNDERLINE = 'U';
    case STRIKETHROUGH = 'D';
    case OVERLINE = 'O';

    /* combined styles */

    case BOLD_ITALIC = 'BI';
    case BOLD_UNDERLINE = 'BU';
    case BOLD_ITALIC_UNDERLINE = 'BIU';
}