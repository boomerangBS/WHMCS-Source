<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\ClientArea\User;

class EmailVerification
{
    const SESSION_DISMISS_NAME = "EmvDismissed";
    public static function dismiss()
    {
        \WHMCS\Session::setAndRelease(self::SESSION_DISMISS_NAME, true);
    }
    public static function isDismissed()
    {
        return (bool) \WHMCS\Session::get(self::SESSION_DISMISS_NAME);
    }
    public static function shouldShowEmailVerificationBanner()
    {
        return \Auth::user() && \Auth::user()->needsToCompleteEmailVerification() && !self::isDismissed();
    }
}

?>