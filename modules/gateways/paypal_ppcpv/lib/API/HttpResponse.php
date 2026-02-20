<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
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