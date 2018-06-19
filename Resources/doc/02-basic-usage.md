Using the bundle
================

The service is just a wrapper to the TCPDF class. You can call on it any method provided by the TCPDF class.

Retrieve the service like any other symfony service:

```php
    $tcpdf = $this->get('tcpdf');
```

This still works for Symfony 4.x, as the 'tcpdf' service is still marked public.
For dependency injection use `jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF` class.

```php
    /**
     * This is a regular Controller action.
     * 
     * @Route("/pdf")
     */
    public function pdfAction(Request $request, \jonasarts\Bundle\TCPDFBundle\TCPDF\TCPDF $pdf)
    {
        // use $pdf like example below

        $pdf->SetCreator(PDF_CREATOR);
        
        [...]
    }
```

In the php code examples, ``$this`` referes to a controller.

```php
    // get the service
    $pdf = $this->container->get('tcpdf');

    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('');
    $pdf->SetTitle('TCPDF Example');
    $pdf->SetSubject('TCPDF Example');
    $pdf->SetKeywords('TCPDF, example');

    // remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set font
    $pdf->SetFont('times', '', 12);

    $pdf->AddPage();

    // set some text to print
    $txt = <<<EOD
TCPDF Example 002

Default page header and footer are disabled using setPrintHeader() and setPrintFooter() methods.
EOD;

    // print a block of text using Write()
    $pdf->Write(0, $txt, '', 0, '', true, 0, false, false, 0);

    $pdf->Output('example_002.pdf', 'I');
```

Read the documentation for the TCPDF class on the [TCPDF Website](http://www.tcpdf.org)!

[Return to the index.](index.md)