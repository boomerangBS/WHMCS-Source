<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment\Ioncube\Inspector\Filter;

class EncoderFingerprintFavorV9 extends AbstractAbsolutelyNonDecodableIterator
{
    public function getAssessment(\WHMCS\Environment\Ioncube\Contracts\InspectedFileInterface $file)
    {
        return $file->getAnalyzer()->versionCompatibilityAssessment($this->getPhpVersion());
    }
}

?>