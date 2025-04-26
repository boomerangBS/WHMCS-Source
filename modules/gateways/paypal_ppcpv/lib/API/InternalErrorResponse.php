<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class InternalErrorResponse extends GenericErrorResponse
{
    public $rawResponse;
    public function __construct(HttpResponse $response)
    {
        $this->rawResponse = $response;
        $this->error = "unknown_response";
        $this->error_description = "Unknown response data was interpreted as an error";
    }
    public function response() : HttpResponse
    {
        return $this->rawResponse;
    }
    public function __toString()
    {
        return sprintf("%s\n%s", parent::__toString(), $this->rawResponse->__toString());
    }
}

?>