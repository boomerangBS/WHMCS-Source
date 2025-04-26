<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment\PayMethod\Traits;

trait PayMethodFactoryTrait
{
    public static function factoryPayMethod(\WHMCS\User\Contracts\UserInterface $client, \WHMCS\User\Contracts\ContactInterface $billingContact = NULL, $description = "")
    {
        $payment = new static();
        $payment->save();
        return $payment->newPayMethod($client, $billingContact, $description);
    }
    public function newPayMethod(\WHMCS\User\Contracts\UserInterface $client, \WHMCS\User\Contracts\ContactInterface $billingContact = NULL, $description = "")
    {
        $payMethod = new \WHMCS\Payment\PayMethod\Model();
        $payMethod->description = $description;
        $payMethod->order_preference = \WHMCS\Payment\PayMethod\Model::totalPayMethodsOnFile($client);
        if(!$billingContact) {
            $billingContact = $client->defaultBillingContact;
        }
        $payMethod->save();
        $payMethod->contact()->associate($billingContact);
        $payMethod->client()->associate($client);
        $payMethod->payment()->associate($this);
        $this->pay_method_id = $payMethod->id;
        $payMethod->push();
        return $payMethod;
    }
}

?>