<?php

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