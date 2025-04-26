<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\API\Entity;

class CardPaymentSource extends AbstractPaymentSource
{
    use \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\PaymentSourceExperienceContextTrait;
    protected $name = "";
    protected $billingAddress = [];
    protected $attributes = [];
    protected $verificationMethod;
    protected function getDetails() : array
    {
        $detail = parent::getDetails();
        if(!empty($this->name)) {
            $detail["name"] = $this->name;
        }
        if(!empty($this->billingAddress)) {
            $detail["billing_address"] = $this->billingAddress;
        }
        if(!empty($this->attributes)) {
            $detail["attributes"] = $this->attributes;
        }
        if(!empty($this->verificationMethod)) {
            $detail["verification_method"] = $this->verificationMethod;
        }
        $this->includeExperienceAsDetails($detail);
        return $detail;
    }
    public function setName($name) : \self
    {
        $this->name = $name;
        return $this;
    }
    public function setBillingAddress($billingAddress1, string $billingAddress2, string $billingCity, string $billingState, string $billingPostcode, string $billingCountry) : \self
    {
        $this->billingAddress = ["address_line_1" => $billingAddress1, "address_line_2" => $billingAddress2, "admin_area_2" => $billingCity, "admin_area_1" => $billingState, "postal_code" => $billingPostcode, "country_code" => $billingCountry];
        return $this;
    }
    public function withBillingContact(\WHMCS\User\Client $client) : \self
    {
        $billingContact = empty($client->billingContactId) ? $client : $client->billingContact;
        $this->withContact($billingContact);
        return $this;
    }
    public function withContact(\WHMCS\User\Contracts\ContactInterface $billingContact) : \self
    {
        $this->setName($billingContact->fullName);
        $this->setBillingAddress($billingContact->address1, $billingContact->address2, $billingContact->city, $billingContact->state, $billingContact->postcode, $billingContact->country);
        return $this;
    }
    public function setCustomerId($customerId) : \self
    {
        $this->attributes["customer"] = ["id" => $customerId];
        return $this;
    }
    public function enableVaulting() : \self
    {
        $this->attributes["vault"] = ["store_in_vault" => "ON_SUCCESS"];
        return $this;
    }
    public function enable3DS() : \self
    {
        $this->attributes["verification"]["method"] = "SCA_WHEN_REQUIRED";
        return $this;
    }
    public function enable3DSAlternate() : \self
    {
        $this->verificationMethod = "SCA_WHEN_REQUIRED";
        return $this;
    }
}

?>