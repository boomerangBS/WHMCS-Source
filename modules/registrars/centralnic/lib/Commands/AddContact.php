<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class AddContact extends AbstractCommand
{
    protected $command = "AddContact";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $firstname, string $lastname, string $company, string $address1, string $address2, string $city, string $state, string $postalCode, string $country, string $email, string $phone, string $fax)
    {
        $this->setParam("NEW", 0)->setParam("PREVERIFY", 1)->setParam("AUTODELETE", 1)->setParam("firstname", $firstname)->setParam("lastname", $lastname)->setParam("organization", $company)->setParam("street0", $address1)->setParam("street1", $address2)->setParam("city", $city)->setParam("state", $state)->setParam("zip", $postalCode)->setParam("country", $country)->setParam("email", $email)->setParam("phone", $phone)->setParam("fax", $fax);
        parent::__construct($api);
    }
    public function asNew() : \self
    {
        $this->setParam("NEW", 1);
        return $this;
    }
}

?>