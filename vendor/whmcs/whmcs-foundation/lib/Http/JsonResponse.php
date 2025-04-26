<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Http;

class JsonResponse extends \Symfony\Component\HttpFoundation\JsonResponse
{
    use DataTrait;
    use PriceDataTrait;
    public function setData($data = [])
    {
        $data = $this->mutatePriceToFull($data);
        $this->setRawData($data);
        parent::setData($data);
        return $this;
    }
}

?>