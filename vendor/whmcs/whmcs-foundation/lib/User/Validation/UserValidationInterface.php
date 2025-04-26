<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Validation;

interface UserValidationInterface
{
    public function isEnabled();
    public function isAutoEnabled();
    public function initiateForUser(\WHMCS\User\User $user) : void;
    public function refreshStatusForUser(\WHMCS\User\User $user) : void;
    public function isRequestComplete(\WHMCS\User\User $user) : \WHMCS\User\User;
    public function getSubmitUrlForUser(\WHMCS\User\User $user) : \WHMCS\User\User;
    public function getSubmitHost();
    public function getViewHost();
    public function getViewUrlForUser(\WHMCS\User\User $user) : \WHMCS\User\User;
    public function getStatusForOutput(\WHMCS\User\User $user) : \WHMCS\User\User;
    public function getStatusColor($status);
    public function sendVerificationEmail(\WHMCS\User\User $user) : \WHMCS\User\User;
    public function shouldShowClientBanner();
    public function dismissClientBanner() : void;
}

?>