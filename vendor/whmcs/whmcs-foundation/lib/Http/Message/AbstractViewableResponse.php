<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Http\Message;

abstract class AbstractViewableResponse
{
    protected $getBodyFromPrivateStream = false;
    public function __construct($data = "", $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
    }
    public function getBody() : \Psr\Http\Message\StreamInterface
    {
        if($this->getBodyFromPrivateStream) {
            return parent::getBody();
        }
        $body = new \Laminas\Diactoros\Stream("php://temp", "wb+");
        $body->write($this->getOutputContent());
        $body->rewind();
        return $body;
    }
    protected abstract function getOutputContent();
}

?>