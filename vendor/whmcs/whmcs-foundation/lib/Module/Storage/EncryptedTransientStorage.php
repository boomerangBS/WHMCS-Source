<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Storage;

class EncryptedTransientStorage extends AbstractDataStorage
{
    private $sessionKey = "transient_module_data";
    protected function readDataFromStorage()
    {
        $allModulesSessionData = \WHMCS\Session::get($this->sessionKey);
        if(empty($allModulesSessionData)) {
            return [];
        }
        $encryption = new \WHMCS\Security\Encryption\Aes();
        $encryption->setKey(hash("sha256", \DI::make("config")->cc_encryption_hash));
        $allModulesData = json_decode($encryption->decrypt($allModulesSessionData), true);
        if(!is_array($allModulesData)) {
            return [];
        }
        return $allModulesData;
    }
    protected function writeDataToStorage(array $allModulesData)
    {
        $encryption = new \WHMCS\Security\Encryption\Aes();
        $encryption->setKey(hash("sha256", \DI::make("config")->cc_encryption_hash));
        \WHMCS\Session::set($this->sessionKey, $encryption->encrypt(json_encode($allModulesData)));
    }
}

?>