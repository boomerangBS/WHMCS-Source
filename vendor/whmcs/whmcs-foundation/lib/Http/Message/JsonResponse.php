<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Http\Message;

class JsonResponse extends \Laminas\Diactoros\Response\JsonResponse
{
    use \WHMCS\Http\DataTrait;
    use \WHMCS\Http\PriceDataTrait;
    public function __construct($data, $status = 200, array $headers = [], $encodingOptions = \Laminas\Diactoros\Response\JsonResponse::DEFAULT_JSON_FLAGS)
    {
        $data = $this->preprocessData($data);
        \Laminas\Diactoros\Response\JsonResponse::__construct($data, $status, $headers, $encodingOptions);
    }
    private function preprocessData($data)
    {
        $data = $this->mutatePriceToFull($data);
        $this->setRawData($data);
        return $data;
    }
    public function withData($data, $encodingOptions = \Laminas\Diactoros\Response\JsonResponse::DEFAULT_JSON_FLAGS)
    {
        $data = $this->preprocessData($data);
        if(is_resource($data)) {
            throw new \InvalidArgumentException("Cannot JSON encode resources");
        }
        json_encode(NULL);
        $json = json_encode($data, $encodingOptions);
        if(JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException(sprintf("Unable to encode data to JSON in %s: %s", "WHMCS\\Http\\Message\\JsonResponse", json_last_error_msg()));
        }
        $body = new \Laminas\Diactoros\Stream("php://temp", "wb+");
        $body->write($json);
        $body->rewind();
        return parent::withBody($body);
    }
    public static function factoryOutputWithExit($data, $status = 200, array $headers = [], $encodingOptions = \Laminas\Diactoros\Response\JsonResponse::DEFAULT_JSON_FLAGS)
    {
        $response = new self($data, $status, $headers, $encodingOptions);
        (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
        \WHMCS\Terminus::getInstance()->doExit();
    }
}

?>