<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
abstract class AbstractResponse
{
    use ResponseCastTrait;
    public abstract function respond(HttpResponse $response) : AbstractResponse;
    public function isError(AbstractResponse $response) : AbstractResponse
    {
        return $response instanceof AbstractErrorResponse;
    }
    public function assertHTTPOK(HttpResponse $response) : \self
    {
        $this->assertHTTPStatus($response, 200);
        return $this;
    }
    public function assertHTTPSuccess(HttpResponse $response) : \self
    {
        if(!$response->isSuccess()) {
            throw new \Exception("HTTP 2xx expected", $response->statusCode);
        }
        return $this;
    }
    public function assertHTTPStatus(HttpResponse $response, int $status) : \self
    {
        if($response->statusCode != $status) {
            throw new \Exception("HTTP " . $status . " expected", $response->statusCode);
        }
        return $this;
    }
    public function withJSON($json) : \self
    {
        $decoded = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($json);
        if($decoded === false) {
            throw new \Exception("Malformed JSON");
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($decoded, $this);
    }
    public function __toString()
    {
        return sprintf("%s%s", static::class, json_encode(get_object_vars($this)));
    }
}

?>