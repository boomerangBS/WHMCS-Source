<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class ModifyContact extends AbstractCommand
{
    protected $command = "ModifyContact";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $contactHandle, string $firstname, string $lastname, string $company, string $address1, string $address2, string $city, string $state, string $postCode, string $country, string $email, string $phone, string $fax)
    {
        $this->setParam("contact", $contactHandle)->setParam("firstname", $firstname)->setParam("lastname", $lastname)->setParam("organization", $company)->setParam("street0", $address1)->setParam("street1", $address2)->setParam("city", $city)->setParam("state", $state)->setParam("zip", $postCode)->setParam("country", $country)->setParam("email", $email)->setParam("phone", $phone)->setParam("fax", $fax);
        parent::__construct($api);
    }
}

?>