<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Table\Contracts;

interface TableInterface
{
    public function list(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse;
}

?>