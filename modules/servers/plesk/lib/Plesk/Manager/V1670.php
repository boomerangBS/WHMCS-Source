<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Plesk_Manager_V1670 extends Plesk_Manager_V1660
{
    protected function _generateCSR($params)
    {
        return parent::_generateCSR($params);
    }
    protected function _installSsl($params)
    {
        return parent::_installSsl($params);
    }
    protected function _getLicenseKey($params)
    {
        $data = Plesk_Registry::getInstance()->api->get_license_key();
        $data = $data->xpath("//server/get/result");
        return $data[0];
    }
}

?>