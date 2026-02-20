<?php

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