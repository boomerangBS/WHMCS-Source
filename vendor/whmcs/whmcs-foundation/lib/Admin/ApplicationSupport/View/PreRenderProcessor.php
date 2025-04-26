<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View;

class PreRenderProcessor
{
    public function process($html)
    {
        if(\App::getCurrentFilename() != "systemintegrationcode") {
            $html = $this->autoAddTokensToForms($html);
        }
        return $this->mutateRelativePathsToAbsolutePaths($html);
    }
    public function mutateRelativePathsToAbsolutePaths($html = "")
    {
        $adminBaseUrl = \WHMCS\Utility\Environment\WebHelper::getBaseUrl(ROOTDIR, $_SERVER["SCRIPT_NAME"]);
        $adminDirectoryName = \App::get_admin_folder_name();
        $adminBaseUrl .= "/" . $adminDirectoryName;
        $adminBaseUrl = preg_replace("#([/]+)#", "/", $adminBaseUrl);
        if(substr($adminBaseUrl, -1) == "/") {
            $adminBaseUrl = substr($adminBaseUrl, 0, -1);
        }
        if(substr($adminBaseUrl, 0, 1) != "/") {
            $adminBaseUrl = "/" . $adminBaseUrl;
        }
        $html = preg_replace("#( src=\"| href=\"| action=\")((?!\\/|http|javascript|\\?|\\#)(?:[^\"]+)\")#i", "\\1" . $adminBaseUrl . "/\\2", $html);
        $html = \WHMCS\View\Asset::conditionalFontawesomeCssInclude($html);
        return $html;
    }
    public function autoAddTokensToForms($html = "")
    {
        return preg_replace("/(<form\\W[^>]*\\bmethod=('|\"|)POST('|\"|)\\b[^>]*>)/i", "\\1\n" . generate_token(), $html);
    }
}

?>