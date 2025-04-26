<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Search\Controller;

abstract class AbstractSearchController implements \WHMCS\Search\ApplicationSupport\Controller\SearchInterface, \WHMCS\Search\SearchInterface
{
    public abstract function getSearchTerm(\WHMCS\Http\Message\ServerRequest $request);
    public abstract function getSearchable();
    public function searchRequest(\WHMCS\Http\Message\ServerRequest $request)
    {
        try {
            $data = $this->getSearchable()->search($this->getSearchTerm($request));
        } catch (\WHMCS\Exception\Information $e) {
            $data = ["warning" => $e->getMessage()];
        } catch (\Exception $e) {
            $data = ["error" => $e->getMessage()];
        }
        return new \WHMCS\Http\Message\JsonResponse($data);
    }
}

?>