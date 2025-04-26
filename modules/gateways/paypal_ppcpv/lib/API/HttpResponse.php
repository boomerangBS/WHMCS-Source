<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class HttpResponse
{
    public $statusCode = 0;
    public $body = "";
    public $headerString = "";
    public function withStatusCode($statusCode) : \self
    {
        $this->statusCode = (int) $statusCode;
        return $this;
    }
    public function withHeaderString($headers) : \self
    {
        $this->headerString = $headers;
        return $this;
    }
    public function withBody($body) : \self
    {
        $this->body = $body;
        return $this;
    }
    public function isOK()
    {
        return $this->statusCode == 200;
    }
    public function isSuccess()
    {
        return 200 <= $this->statusCode && $this->statusCode < 300;
    }
    public function isError()
    {
        return $this->isErrorClient() || $this->isErrorServer();
    }
    public function isErrorClient()
    {
        return 400 <= $this->statusCode && $this->statusCode < 500;
    }
    public function isErrorServer()
    {
        return 500 <= $this->statusCode && $this->statusCode < 600;
    }
    public function __toString()
    {
        return sprintf("[%d]%s\n%s", $this->statusCode, $this->body, $this->headerString);
    }
}

?>