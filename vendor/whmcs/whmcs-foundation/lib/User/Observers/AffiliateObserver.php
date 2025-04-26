<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Observers;

class AffiliateObserver
{
    public function deleting(\WHMCS\User\Client\Affiliate $affiliate) : void
    {
        $affiliate->hits()->delete();
        $affiliate->pending()->delete();
        $affiliate->referrers()->delete();
        $affiliate->history()->delete();
        $affiliate->withdrawals()->delete();
        $affiliate->accounts()->delete();
    }
}

?>