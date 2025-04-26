<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class AcceptClaimRequest extends AbstractRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    use RequestSendReadyAllPropertiesTrait;
    protected $id = "";
    protected $note = "";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->contentJSON()->acceptJSON()->post("/v1/customer/disputes/" . $this->id . "/accept-claim", $this->payload());
    }
    public function payload()
    {
        return json_encode(["note" => $this->note]);
    }
    public function responseType() : AbstractResponse
    {
        return new AcceptClaimResponse();
    }
    public function setIdentifier($id) : \self
    {
        $this->id = $id;
        return $this;
    }
    public function setNote($note) : \self
    {
        $this->note = $note;
        return $this;
    }
}

?>