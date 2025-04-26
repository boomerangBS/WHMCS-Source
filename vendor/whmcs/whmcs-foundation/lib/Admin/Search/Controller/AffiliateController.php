<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Search\Controller;

class AffiliateController extends AbstractSearchController
{
    public function getSearchTerm(\WHMCS\Http\Message\ServerRequest $request)
    {
        return $request->get("dropdownsearchq", NULL);
    }
    public function getSearchable()
    {
        return new \WHMCS\Search\Affiliate();
    }
    public function search($searchTerm = NULL)
    {
        return $this->getSearchable()->search($searchTerm);
    }
}

?>