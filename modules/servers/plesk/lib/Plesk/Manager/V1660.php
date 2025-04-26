<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Plesk_Manager_V1660 extends Plesk_Manager_V1640
{
    protected function _getAddAccountParams($params)
    {
        $result = parent::_getAddAccountParams($params);
        $result["powerUser"] = "on" === $params["configoption4"] ? "true" : "false";
        return $result;
    }
    protected function _addAccount($params)
    {
        return parent::_addAccount($params);
    }
}

?>