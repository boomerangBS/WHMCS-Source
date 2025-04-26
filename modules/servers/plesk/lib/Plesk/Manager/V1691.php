<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Plesk_Manager_V1691 extends Plesk_Manager_V1680
{
    protected function _getExtensions($params)
    {
        $data = Plesk_Registry::getInstance()->api->get_extensions();
        $data = $data->xpath("//extension/get/result");
        return $data[0];
    }
    protected function _callExtension($params)
    {
        $data = Plesk_Registry::getInstance()->api->call_extension($params);
        $data = $data->xpath("//extension/call/result");
        return $data[0];
    }
    protected function _callExtensionAttr($params)
    {
        $data = Plesk_Registry::getInstance()->api->call_extension_attr($params);
        foreach ($data->xpath("//extension/call") as $result) {
            $status = (string) $result->status;
            if($status !== "" && $status !== Plesk_Api::STATUS_OK) {
                throw new Exception((string) $result->errtext);
            }
        }
        $data = $data->xpath("//extension/call/result");
        return $data[0];
    }
}

?>