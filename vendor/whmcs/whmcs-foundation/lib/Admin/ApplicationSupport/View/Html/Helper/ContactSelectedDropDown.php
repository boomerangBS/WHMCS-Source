<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html\Helper;

class ContactSelectedDropDown extends ClientSelectedDropDown
{
    protected $includeClientAsOption = false;
    protected $selectorClass = "selectize-contact-search";
    protected $client;
    public function __construct(\WHMCS\User\Client $client, $includeClientAsOption = false, $nameAttribute = "userid", $selected = 0)
    {
        $this->client = $client;
        if($includeClientAsOption) {
            $this->includeClientAsOption = true;
        }
        parent::__construct($nameAttribute, $selected);
    }
    public function setSelectedClientId($selectedClientId)
    {
        $this->selectedClientId = $selectedClientId;
        return $this;
    }
    protected function getSelectOptionsForClientId($clientId = 0)
    {
        $selectOptions = [];
        $contacts = $this->client->contacts;
        if($this->includeClientAsOption) {
            $contacts->prepend($this->client);
        }
        foreach ($contacts as $contact) {
            $id = "";
            if($contact instanceof \WHMCS\User\Client) {
                $id = "client-";
            }
            $id .= $contact->id;
            $selectOptions[$id] = sprintf("%s %s", $contact->fullName, $contact->companyname ? " (" . $contact->companyname . ")" : "");
        }
        return $selectOptions;
    }
}

?>