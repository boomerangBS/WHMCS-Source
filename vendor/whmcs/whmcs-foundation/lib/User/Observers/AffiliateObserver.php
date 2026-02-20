<?php

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