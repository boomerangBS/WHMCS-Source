<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class ListDisputesRequest extends SimpleGetRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $nextPageToken;
    protected $perPage = 50;
    public function send() : HttpResponse
    {
        return $this->acceptJSON()->get($this->getEndpoint());
    }
    public function responseType() : AbstractResponse
    {
        return new ListDisputesResponse();
    }
    private function getEndpoint()
    {
        $endpoint = "/v1/customer/disputes/?page_size=" . $this->perPage;
        if(!is_null($this->nextPageToken)) {
            $endpoint .= "&next_page_token=" . $this->nextPageToken;
        }
        return $endpoint;
    }
    public function setNextPageToken($nextPageToken) : \self
    {
        $this->nextPageToken = $nextPageToken;
        return $this;
    }
    public function withPerPage($count) : \self
    {
        if(50 < $count) {
            throw new \InvalidArgumentException("Page count cannot be greater than 50.");
        }
        $this->perPage = $count;
        return $this;
    }
}

?>