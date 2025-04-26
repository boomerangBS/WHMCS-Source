<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Search\Controller;

class UserController extends AbstractSearchController
{
    public function getSearchTerm(\WHMCS\Http\Message\ServerRequest $request) : array
    {
        return ["searchTerm" => $request->get("search", ""), "clientId" => $request->get("clientId", 0)];
    }
    public function getSearchable() : \WHMCS\Search\User
    {
        return new \WHMCS\Search\User();
    }
    public function search($searchTerm) : array
    {
        if(is_array($searchTerm)) {
            $clientId = isset($searchTerm["clientId"]) ? $searchTerm["clientId"] : 0;
            $searchTerm = isset($searchTerm["searchTerm"]) ? $searchTerm["searchTerm"] : "";
        } else {
            $clientId = 0;
        }
        $searchFor = ["clientId" => $clientId, "searchTerm" => $searchTerm];
        return $this->getSearchable()->search($searchFor);
    }
}

?>