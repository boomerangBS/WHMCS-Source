<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
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