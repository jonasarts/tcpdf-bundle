<?php

declare(strict_types=1);

namespace jonasarts\Bundle\TCPDFBundle\TCPDF\Utils;

/**
 * BankingUtils
 */
abstract class BankingUtils
{
    /**
     * Creates Modulo10 recursive check digit
     *
     * as found on http://www.developers-guide.net/forums/5431,modulo10-rekursiv
     */
    static public function modulo10(string $number): int
    {
        $table = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];
        $next = 0;
        
        for ($i=0; $i<strlen($number); $i++) {
            $next = $table[($next + (int)substr($number, $i, 1)) % 10];
        }

        return (10 - $next) % 10;   
    }

    /**
     * Displays a string in blocks of a certain size.
     *
     * Default: right aligned 5char blocks
     * For IBAN: breakStringIntoBlocks($iban, 4, false) = left aligned 4char blocks
     */
    static public function breakStringIntoBlocks(string $string, int $blockSize = 5, bool $alignFromRight = true): string
    {
        // if requested, lets reverse the string
        if ($alignFromRight) {
            $string = strrev($string);
        }

        // chop it into blocks
        $string = trim(chunk_split($string, $blockSize, ' '));

        // re-reverse, if needed
        if ($alignFromRight) {
            $string = strrev($string);
        }

        return $string;
    }

    /**
     * Returns 27 characters
     * - no longer returns 32 characters !!!
     *
     * @param string|null $bankingCustomerIdentification 6 digits (exact)
     * @param string $referenceNumber 20 digits (max))
     * @return string
     * @throws \Exception
     */
    static public function generateESRReferenceNumber(?string $bankingCustomerIdentification, string $referenceNumber): string
    {
        if (!is_null($bankingCustomerIdentification)) {
            if (strlen($bankingCustomerIdentification) > 6) {
                throw new \Exception('banking customer identification has wrong size; > 6');
            }
            if (strlen($bankingCustomerIdentification) < 6) {
                throw new \Exception('banking customer identification has wrong size; < 6');
            }
            if (strlen($referenceNumber) > (26 - strlen($bankingCustomerIdentification))) {
                throw new \Exception('reference number has wrong size; > 26 - 6');
            }
        } else {
            if (strlen($referenceNumber) > 26) {
                throw new \Exception('reference number has wrong size; > 26');
            }
        }

        if (!is_null($bankingCustomerIdentification)) {
            // get reference number and fill with zeros
            $completeReferenceNumber = str_pad($referenceNumber, (26 - strlen($bankingCustomerIdentification)), '0', STR_PAD_LEFT);

            // prepend customer identification code
            // 26 =                    6                                26-6=20
            $completeReferenceNumber = $bankingCustomerIdentification . $completeReferenceNumber;
        } else {
            $completeReferenceNumber = str_pad($referenceNumber, 26, '0', STR_PAD_LEFT);
        }

        // add check digit          +1
        $completeReferenceNumber .= static::modulo10($completeReferenceNumber);

        //     27
        return $completeReferenceNumber;
    }
}
