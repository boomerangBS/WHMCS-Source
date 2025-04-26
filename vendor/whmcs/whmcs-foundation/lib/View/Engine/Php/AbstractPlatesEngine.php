<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Engine\Php;

abstract class AbstractPlatesEngine extends \League\Plates\Engine implements \WHMCS\View\Engine\VariableAccessorInterface
{
    public function __construct($directory = NULL, $fileExtension = "php")
    {
        if(is_null($directory)) {
            $directory = ROOTDIR . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "views";
        }
        parent::__construct($directory, $fileExtension);
        $this->addData($this->getDefaultVariables());
    }
    public function getDefaultVariables()
    {
        $assetHelper = \DI::make("asset");
        return ["WEB_ROOT" => $assetHelper->getWebRoot(), "BASE_PATH_CSS" => $assetHelper->getCssPath(), "BASE_PATH_JS" => $assetHelper->getJsPath(), "BASE_PATH_FONTS" => $assetHelper->getFontsPath(), "BASE_PATH_IMG" => $assetHelper->getImgPath()];
    }
    public function assign($tpl_var, $value = NULL, $nocache = false)
    {
        if(!is_array($tpl_var)) {
            $data = [$tpl_var => $value];
        } else {
            $data = $tpl_var;
        }
        $this->addData($data);
    }
}

?>