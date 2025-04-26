<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Http;

trait PriceDataTrait
{
    public function mutatePriceToFull($data = [])
    {
        array_walk_recursive($data, function (&$item) {
            if($item instanceof \WHMCS\View\Formatter\Price) {
                $item = $item->toFull();
            }
        });
        return $data;
    }
}

?>