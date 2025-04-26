<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Search\Controller;

class ServiceController extends AbstractSearchController
{
    public function getSearchTerm(\WHMCS\Http\Message\ServerRequest $request) : array
    {
        return ["searchTerm" => $request->get("search", NULL), "clientId" => $request->get("clientId", 0)];
    }
    public function getSearchable() : \WHMCS\Search\SearchInterface
    {
        return new \WHMCS\Search\Service();
    }
    public function search($searchTerm) : array
    {
        if(is_array($searchTerm)) {
            $clientId = $searchTerm["clientId"] ?? NULL;
            $searchTerm = $searchTerm["searchTerm"] ?? NULL;
        } else {
            $clientId = NULL;
        }
        $searchFor = ["clientId" => $clientId, "searchTerm" => $searchTerm];
        return $this->getSearchable()->search($searchFor);
    }
}

?>