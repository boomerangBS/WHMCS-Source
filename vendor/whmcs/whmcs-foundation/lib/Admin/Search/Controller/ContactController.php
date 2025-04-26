<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Search\Controller;

class ContactController extends AbstractSearchController
{
    public function getSearchTerm(\WHMCS\Http\Message\ServerRequest $request)
    {
        return ["searchTerm" => $request->get("dropdownsearchq", NULL), "clientId" => $request->get("clientId", NULL)];
    }
    public function getSearchable()
    {
        return new \WHMCS\Search\Contact();
    }
    public function search($searchTerm = NULL)
    {
        if(is_array($searchTerm)) {
            $clientId = isset($searchTerm["clientId"]) ? $searchTerm["clientId"] : NULL;
            $searchTerm = isset($searchTerm["searchTerm"]) ? $searchTerm["searchTerm"] : NULL;
        } else {
            $clientId = NULL;
        }
        $searchFor = ["clientId" => $clientId, "searchTerm" => $searchTerm];
        return $this->getSearchable()->search($searchFor);
    }
}

?>