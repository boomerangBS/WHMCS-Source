<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer\Composer;

class UpdateEnvironment
{
    public static function initEnvironment($updateTempDir)
    {
        $environmentErrors = [];
        if(empty($updateTempDir) || !is_dir($updateTempDir)) {
            $environmentErrors[] = \AdminLang::trans("update.missingUpdateTempDir");
        } elseif(!is_writable($updateTempDir)) {
            $environmentErrors[] = \AdminLang::trans("update.updateTempDirNotWritable");
        }
        return $environmentErrors;
    }
}

?>