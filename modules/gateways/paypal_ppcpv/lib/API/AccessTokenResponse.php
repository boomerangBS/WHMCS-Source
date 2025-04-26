<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class AccessTokenResponse extends AbstractResponse
{
    public $scope = "";
    public $access_token = "";
    public $token_type = "";
    public $app_id = "";
    public $expires_in = -1;
    public $nonce = "";
    public function token()
    {
        return $this->access_token;
    }
    public function expiresInSeconds(\DateTimeImmutable $from) : int
    {
        if(is_null($from)) {
            return (int) $this->expires_in;
        }
        $expiryTimestamp = $this->expiryDate()->getTimestamp();
        $seconds = $expiryTimestamp - $from->getTimestamp();
        if($seconds < 0) {
            return -1;
        }
        return $seconds;
    }
    public function expiresInInterval() : \DateInterval
    {
        return new \DateInterval("PT" . $this->expires_in . "S");
    }
    public function expiryDate() : \DateTimeImmutable
    {
        return $this->nonceDateTime()->add($this->expiresInInterval());
    }
    public function nowUTC() : \DateTime
    {
        return new \DateTime("now", new \DateTimeZone("UTC"));
    }
    public function respond(HttpResponse $response) : AbstractResponse
    {
        return $this->assertHTTPOK($response)->withJSON($response->body);
    }
    public function isStale(\DateInterval $within) : \DateInterval
    {
        if($this->nonce == "") {
            return true;
        }
        $nowish = $this->nowUTC();
        if(!is_null($within)) {
            $nowish->add($within);
        }
        return $this->expiryDate() < $nowish;
    }
    public function nonceDateTime() : \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromFormat(\DateTimeInterface::ISO8601, sprintf("%s+0000", substr($this->nonce, 0, 19)), new \DateTimeZone("UTC"));
    }
    public function pack()
    {
        return json_encode(get_object_vars($this));
    }
    public function unpack($packed) : \self
    {
        $o = new static();
        $decoded = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($packed);
        if($decoded === false) {
            return $o;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($decoded, $o);
    }
}

?>