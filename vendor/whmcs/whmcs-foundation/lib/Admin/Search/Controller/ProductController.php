<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Search\Controller;

class ProductController extends AbstractSearchController
{
    public function getSearchTerm(\WHMCS\Http\Message\ServerRequest $request) : array
    {
        return ["searchTerm" => $request->get("search", NULL), "productId" => $request->get("productId", 0)];
    }
    public function getSearchable() : \WHMCS\Search\Product
    {
        return new \WHMCS\Search\Product();
    }
    public function search($searchTerm) : array
    {
        if(is_array($searchTerm)) {
            $productId = $searchTerm["productId"] ?? NULL;
            $searchTerm = $searchTerm["searchTerm"] ?? NULL;
        } else {
            $productId = NULL;
        }
        $searchFor = ["productId" => $productId, "searchTerm" => $searchTerm];
        return $this->getSearchable()->search($searchFor);
    }
}

?>