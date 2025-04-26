<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Api\NG\Versions\V2;

abstract class AbstractApiController
{
    protected function getResponseData($data) : array
    {
        $metadata = [];
        if($this instanceof PagedResponseInterface && $this->hasPageInformation()) {
            $metadata = array_merge($metadata, ["page" => $this->getPageNumber(), "total_pages" => $this->getPageCount()]);
        }
        return ["meta" => $metadata, "data" => $data];
    }
    protected function createResponse($data = 200, $status = [], array $headers) : \WHMCS\Http\Message\JsonResponse
    {
        return new \WHMCS\Http\Message\JsonResponse($this->getResponseData($data), $status, $headers);
    }
}

?>