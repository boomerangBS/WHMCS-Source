<?php

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