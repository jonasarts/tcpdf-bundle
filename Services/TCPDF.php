<?php

/*
 * This file is part of the TCPDF bundle package.
 *
 * (c) Jonas Hauser <symfony@jonasarts.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace jonasarts\Bundle\TCPDFBundle\Services;

require_once __DIR__ . '/../lib/tcpdf.php';

/**
 * TCPDF Service
 */
class TCPDF extends \TCPDF
{
    public function __construct()
    {
        // construct the TCPDF class
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }
}
