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

namespace jonasarts\Bundle\TCPDFBundle\TCPDF;

//require_once __DIR__ . '/../lib/technickcom/tcpdf/tcpdf.php';

/**
 * TCPDF Service
 */
class TCPDF extends \TCPDF
{
    public function __construct($orientation='P', $unit='mm', $format='A4')
    {
        // construct the TCPDF class
        // __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false)
        
        //parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        parent::__construct($orientation, $unit, $format, true, 'UTF-8', false);
    }

    public static function createA3($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A3');
    }

    public static function createA4($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A4');
    }

    public static function createA5($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A5');
    }

    public static function createA6($orientation='P'): self
    {
        return new self($orientation, 'mm', 'A6');
    }
}
