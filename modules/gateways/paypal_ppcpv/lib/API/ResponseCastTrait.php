<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
trait ResponseCastTrait
{
    public function cast(AbstractResponse $r) : \self
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($r, $this);
    }
}

?>