<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html\Helper;

class ClientSelectedDropDown extends ClientSearchDropdown
{
    protected $selectedClientId = 0;
    public function __construct($nameAttribute = "userid", $selectedClientId = 0)
    {
        parent::__construct($nameAttribute, $selectedClientId, [], \AdminLang::trans("global.typeToSearchClients"), "id", 0);
        $this->setSelectedClientId($selectedClientId);
    }
    public function getSelectedClientId()
    {
        return $this->selectedClientId;
    }
    public function setSelectedClientId($selectedClientId)
    {
        $this->selectedClientId = (int) $selectedClientId;
        return $this;
    }
    protected function getSelectOptionsForClientId($clientId = 0)
    {
        $selectOptions = [];
        if($clientId) {
            $client = \WHMCS\Database\Capsule::table("tblclients")->find($clientId, ["firstname", "lastname", "companyname", "email"]);
            if($client) {
                $selectOptions[$clientId] = sprintf("%s %s%s", $client->firstname, $client->lastname, $client->companyname ? " (" . $client->companyname . ")" : "");
            }
        }
        return $selectOptions;
    }
    protected function getHtmlSelectOptions()
    {
        $this->setSelectOptions($this->getSelectOptionsForClientId($this->getSelectedClientId()));
        return parent::getHtmlSelectOptions();
    }
}

?>