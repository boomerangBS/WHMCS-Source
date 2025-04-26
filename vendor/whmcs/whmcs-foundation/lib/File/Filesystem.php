<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\File;

class Filesystem extends \League\Flysystem\Filesystem
{
    use StorageErrorHandlingTrait;
    public function isLocalAdapter()
    {
        return $this->getAdapter() instanceof \League\Flysystem\Adapter\Local;
    }
    private function withoutAsserts(callable $action)
    {
        $previousAssertSetting = $this->config->get("disable_asserts");
        $this->config->set("disable_asserts", true);
        try {
            $result = $action();
        } finally {
            $this->config->set("disable_asserts", $previousAssertSetting);
        }
    }
    public function deleteAllowNotPresent($path)
    {
        return $this->withoutAsserts(function () use($path) {
            return $this->delete($path);
        });
    }
    public function getSizeStrict($path)
    {
        return $this->withoutAsserts(function () use($path) {
            return $this->getSize($path);
        });
    }
}

?>