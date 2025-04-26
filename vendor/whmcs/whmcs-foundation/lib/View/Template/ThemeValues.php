<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Template;

class ThemeValues extends AbstractConfigValues
{
    protected function defaultPathMap() : array
    {
        return ["css" => "/css", "fonts" => "/fonts", "img" => "/img", "js" => "/js"];
    }
    protected function calculateValues() : array
    {
        $theme = $this->getTemplate();
        return ["template" => $theme->getName(), "webroot" => $this->getWebRoot(), "theme" => $this->defaultValues()];
    }
}

?>