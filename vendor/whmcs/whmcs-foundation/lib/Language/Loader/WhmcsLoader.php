<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Language\Loader;

class WhmcsLoader extends \Symfony\Component\Translation\Loader\ArrayLoader implements \Symfony\Component\Translation\Loader\LoaderInterface
{
    protected $globalVariable;
    public function __construct($globalVariable = "_LANG")
    {
        $this->globalVariable = $globalVariable;
    }
    public function load($resource, $locale, $domain = "messages")
    {
        ${$this->globalVariable} = [];
        ob_start();
        require $resource;
        ob_end_clean();
        return parent::load(${$this->globalVariable}, $locale, $domain);
    }
}

?>