<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class VaultPaymentTokenCreatedEvent extends AbstractWebhookEvent
{
    public $payment_source;
    public $id = "";
    public $customer;
    protected $moduleName;
    protected $expectedPayloadProperties = ["id", "customer", "customer->id", "payment_source"];
    public function orderIdentifier()
    {
        return $this->request->resource->metadata->order_id ?? "";
    }
    public function resourceMetaData()
    {
        return $this->request->resource->metadata ?? NULL;
    }
    public function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\VaultPaymentTokenCreated();
    }
    public function initiatingModule()
    {
        if(is_null($this->moduleName)) {
            $paySource = key(get_object_vars($this->payment_source));
            if($paySource == "card") {
                $this->moduleName = \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME;
            } else {
                $this->moduleName = \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
            }
        }
        return $this->moduleName;
    }
    public function billingContactFromCard($paymentSourceCard) : \WHMCS\User\Client\Contact
    {
        $contact = new \WHMCS\User\Client\Contact();
        $name = $this->normalizeName($paymentSourceCard->name ?? "");
        $contact->firstName = $name->firstName;
        $contact->lastName = $name->lastName;
        $contact->address1 = $paymentSourceCard->billing_address->address_line_1 ?? "";
        $contact->address2 = $paymentSourceCard->billing_address->address_line_2 ?? "";
        $contact->city = $paymentSourceCard->billing_address->admin_area_2 ?? "";
        $contact->state = $paymentSourceCard->billing_address->admin_area_1 ?? "";
        $contact->postcode = $paymentSourceCard->billing_address->postal_code ?? "";
        $contact->country = $paymentSourceCard->billing_address->country_code ?? "";
        return $contact;
    }
    public function paymentSourceCard()
    {
        return $this->payment_source->card ?? NULL;
    }
    public function paymentSourcePayPal()
    {
        return $this->payment_source->paypal ?? NULL;
    }
    private function normalizeName(string $fullName)
    {
        $name = new func_num_args();
        $name->lastName = $fullName;
        $nameAsArray = explode(" ", $fullName);
        if(1 < count($nameAsArray)) {
            $name->lastName = array_pop($nameAsArray);
            $name->firstName = implode(" ", $nameAsArray);
        }
        return $name;
    }
    public function packEventRequest()
    {
        $payload = json_decode(parent::packEventRequest());
        unset($payload->resource->id);
        foreach ($payload->resource->links as $link) {
            unset($link->href);
        }
        return json_encode($payload);
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F4150492F5661756C745061796D656E74546F6B656E437265617465644576656E742E7068703078376664353934323461633331_
{
    public $firstName = "";
    public $lastName = "";
}

?>