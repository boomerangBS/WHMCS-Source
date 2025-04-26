<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Template;

class OrderFormValues extends AbstractConfigValues
{
    protected function defaultPathMap() : array
    {
        return ["css" => "/css", "img" => "/img", "js" => "/js"];
    }
    protected function calculateValues() : array
    {
        return ["orderform" => $this->defaultValues()];
    }
}

?>