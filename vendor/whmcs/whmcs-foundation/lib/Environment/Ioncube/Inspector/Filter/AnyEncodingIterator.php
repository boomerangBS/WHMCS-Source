<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment\Ioncube\Inspector\Filter;

class AnyEncodingIterator extends AbstractCacheIterator
{
    public function accept(\WHMCS\Environment\Ioncube\Contracts\InspectedFileInterface $current)
    {
        if(in_array($current->getEncoderVersion(), [\WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_NONE])) {
            return false;
        }
        return true;
    }
}

?>