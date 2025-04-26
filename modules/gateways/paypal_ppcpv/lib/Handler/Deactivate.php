<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class Deactivate extends AbstractHandler
{
    public function handle() : void
    {
        $unlinkHandler = $this->as("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\Unlink");
        foreach (\WHMCS\Module\Gateway\paypal_ppcpv\Environment::eachCredentials($this->moduleConfiguration) as $env) {
            $unlinkHandler->unlink($env);
        }
    }
}

?>