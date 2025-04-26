<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Apps\Hero;

class Model
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function getImageUrl()
    {
        return $this->data["image"];
    }
    public function hasTargetAppKey()
    {
        return 0 < strlen($this->getTargetAppKey());
    }
    public function getTargetAppKey()
    {
        return $this->data["app_key"];
    }
    public function hasRemoteUrl()
    {
        return 0 < strlen($this->getRemoteUrl());
    }
    public function getRemoteUrl()
    {
        return $this->data["remote_url"] ?? "";
    }
}

?>