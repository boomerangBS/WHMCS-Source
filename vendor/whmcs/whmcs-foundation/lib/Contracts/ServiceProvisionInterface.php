<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Contracts;

interface ServiceProvisionInterface
{
    public function provision($model, array $params);
    public function configure($model, array $params);
    public function cancel($model, array $params);
    public function renew($model, array $response);
    public function install(\WHMCS\ServiceInterface $model, array $params);
}

?>