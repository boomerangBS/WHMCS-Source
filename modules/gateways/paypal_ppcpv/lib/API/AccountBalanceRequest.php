<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class AccountBalanceRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $asOfTime;
    protected $currency = "";
    public function send() : HttpResponse
    {
        return $this->acceptJSON()->contentJSON()->get("/v1/reporting/balances", $this->payload());
    }
    protected function payload()
    {
        $query = [];
        if(!is_null($this->asOfTime)) {
            $query["as_of_time"] = $this->asOfTime;
        }
        $query["currency"] = "ALL";
        if($this->currency != "") {
            $query["currency"] = $this->currency;
        }
        return "?" . http_build_query($query);
    }
    public function responseType() : AbstractResponse
    {
        return new AccountBalanceResponse();
    }
    public function withAsOf(\DateTimeImmutable $time) : \self
    {
        $this->asOfTime = $time->format("c");
        return $this;
    }
    public function withCurrency($code) : \self
    {
        $this->currency = $code;
        return $this;
    }
}

?>