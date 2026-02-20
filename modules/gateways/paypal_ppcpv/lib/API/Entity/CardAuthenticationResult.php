<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API\Entity;
class CardAuthenticationResult
{
    public $liability_shift = "";
    public $three_d_secure;
    public static function factory($auth) : \self
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($auth, new self());
    }
    public function withJSON($json) : \self
    {
        $decoded = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($json);
        if($decoded === false) {
            throw new \Exception("Malformed JSON");
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($decoded, $this);
    }
    public function isApproved()
    {
        if($this->enrollmentStatus() == "N") {
            return true;
        }
        if(in_array($this->enrollmentStatus(), ["U", "B"]) && $this->liabilityShift() == "NO") {
            return true;
        }
        if(in_array($this->liabilityShift(), ["YES", "POSSIBLE"])) {
            return true;
        }
        return false;
    }
    public function liabilityShift()
    {
        return $this->liability_shift;
    }
    public function enrollmentStatus()
    {
        return $this->three_d_secure->enrollment_status ?? "";
    }
    public function authenticationStatus()
    {
        return $this->three_d_secure->authentication_status ?? "";
    }
}

?>