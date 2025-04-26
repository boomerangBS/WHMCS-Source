<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment\Ioncube\Inspector\Filter;

abstract class AbstractAbsolutelyNonDecodableIterator extends AbstractCacheIterator
{
    public function accept(\WHMCS\Environment\Ioncube\Contracts\InspectedFileInterface $current)
    {
        if($this->getAssessment($current) === \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO) {
            return true;
        }
        return false;
    }
    public abstract function getAssessment(\WHMCS\Environment\Ioncube\Contracts\InspectedFileInterface $file);
}

?>