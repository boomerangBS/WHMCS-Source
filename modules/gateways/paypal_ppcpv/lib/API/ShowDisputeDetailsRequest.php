<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class ShowDisputeDetailsRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $disputeId = "";
    public function send() : HttpResponse
    {
        return $this->acceptJSON()->get("/v1/customer/disputes/" . $this->disputeId);
    }
    public function responseType() : AbstractResponse
    {
        return new ShowDisputeDetailsResponse();
    }
    public function setIdentifier($disputeId) : \self
    {
        $this->disputeId = $disputeId;
        return $this;
    }
}

?>