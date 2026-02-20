<?php

namespace WHMCS\Module\Registrar\CentralNic\Api;

interface ParserInterface
{
    public function buildPayload($params) : array;
    public function parseResponse($response) : array;
    public function getResponseDataValue(string $key, array $data);
    public function getResponseCode($response) : int;
    public function getResponseDescription($response) : array;
    public function getResponseData($response) : array;
}

?>