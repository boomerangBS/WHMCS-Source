<?php

namespace WHMCS\Module\Registrar\GoDaddy\Api;

class Response
{
    public $headers;
    public $status_code;
    public $body;
    public function __construct(\Psr\Http\Message\ResponseInterface $response)
    {
        $this->headers = $response->getHeaders();
        $this->status_code = $response->getStatusCode();
        $this->body = json_decode($response->getBody());
    }
}

?>