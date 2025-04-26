<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Validation;

trait UserValidationClientBannerTrait
{
    public function dismissClientBanner() : void
    {
        \WHMCS\Session::setAndRelease(self::CLIENT_BANNER_DISMISS_VAR_NAME, true);
    }
    private function isClientBannerDismissed()
    {
        return (bool) \WHMCS\Session::get(self::CLIENT_BANNER_DISMISS_VAR_NAME);
    }
    public function shouldShowClientBanner()
    {
        $loggedInUser = \Auth::user();
        return $loggedInUser && $loggedInUser->isValidationPending() && !$this->isClientBannerDismissed();
    }
}

?>