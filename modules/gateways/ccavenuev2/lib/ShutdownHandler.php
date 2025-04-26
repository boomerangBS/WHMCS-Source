<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\CCAvenueV2;

class ShutdownHandler extends \Whoops\Handler\CallbackHandler
{
    public function handle()
    {
        if(is_callable($this->callable)) {
            return parent::handle();
        }
    }
    public function unregister() : void
    {
        $this->callable = NULL;
    }
}

?>