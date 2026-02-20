<?php

namespace WHMCS\Module\Contracts;

interface Oauth2SenderModuleInterface extends SenderModuleInterface
{
    public function setMailModuleInstance(\WHMCS\Module\Mail $mail) : void;
}

?>