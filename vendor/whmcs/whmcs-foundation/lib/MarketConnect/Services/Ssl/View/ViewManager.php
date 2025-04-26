<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\MarketConnect\Services\Ssl\View;

abstract class ViewManager
{
    protected $model;
    protected $ssl;
    protected $lang;
    protected abstract function getTranslator() : \WHMCS\Language\AbstractLanguage;
    public function __construct(\WHMCS\ServiceInterface $model, \WHMCS\Service\Ssl $ssl)
    {
        $this->model = $model;
        $this->ssl = $ssl;
        $this->lang = $this->getTranslator();
    }
    public function renderDomainControlValidation()
    {
        return "";
    }
    public function trans($key, $parameters)
    {
        return $this->lang->trans($key, $parameters);
    }
}

?>