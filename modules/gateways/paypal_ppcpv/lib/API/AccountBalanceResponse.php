<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class AccountBalanceResponse extends AbstractResponse
{
    public $balances;
    public $account_id;
    public $as_of_time;
    public $last_refresh_time;
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPSuccess($response)->withJSON($response->body);
    }
}

?>