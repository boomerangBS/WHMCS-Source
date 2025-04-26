<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment;

class PaymentServiceProvider extends \WHMCS\Application\Support\ServiceProvider\AbstractServiceProvider
{
    public function register()
    {
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([Contracts\PayMethodTypeInterface::TYPE_BANK_ACCOUNT => "WHMCS\\Payment\\PayMethod\\Adapter\\BankAccount", Contracts\PayMethodTypeInterface::TYPE_REMOTE_BANK_ACCOUNT => "WHMCS\\Payment\\PayMethod\\Adapter\\RemoteBankAccount", Contracts\PayMethodTypeInterface::TYPE_CREDITCARD_LOCAL => "WHMCS\\Payment\\PayMethod\\Adapter\\CreditCard", Contracts\PayMethodTypeInterface::TYPE_CREDITCARD_REMOTE_MANAGED => "WHMCS\\Payment\\PayMethod\\Adapter\\RemoteCreditCard", "Client" => "WHMCS\\User\\Client", "Contact" => "WHMCS\\User\\Client\\Contact"]);
    }
}

?>