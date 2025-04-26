<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Search;

class Affiliate implements SearchInterface
{
    public function search($searchTerm = NULL)
    {
        $searchTerm = isset($searchTerm) ? $searchTerm : "";
        $data = $this->fuzzySearch($searchTerm);
        return $data;
    }
    public function fuzzySearch($searchTerm)
    {
        $searchResults = [];
        $matchingAffiliates = \WHMCS\User\Client\Affiliate::with("client");
        if($searchTerm) {
            $matchingAffiliates->whereHas("client", function ($query) use($searchTerm) {
                $query->where(\WHMCS\Database\Capsule::raw("CONCAT(firstname, ' ', lastname)"), "LIKE", "%" . $searchTerm . "%");
            });
        } else {
            $matchingAffiliates->limit(30);
        }
        foreach ($matchingAffiliates->get() as $affiliate) {
            $searchResults[] = ["aff_id" => $affiliate->id, "name" => \WHMCS\Input\Sanitize::decode($affiliate->client->firstName . " " . $affiliate->client->lastName)];
        }
        return $searchResults;
    }
}

?>