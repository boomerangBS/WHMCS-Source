<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

abstract class AbstractHandler
{
    protected $module;
    protected $systemConfiguration;
    protected $moduleConfiguration;
    protected $log;
    public function __construct(\WHMCS\Module\Gateway $module, \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration $sys, \WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration $config)
    {
        $this->module = $module;
        $this->systemConfiguration = $sys;
        $this->withModuleConfiguration($config);
        $this->log = \WHMCS\Module\Gateway\paypal_ppcpv\Logger::factory($config, $module);
    }
    public function withModuleConfiguration(\WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration $config)
    {
        $this->moduleConfiguration = $config;
        return $this;
    }
    public static function factory($moduleFunction, $module, $sys, $moduleConfiguration) : \self
    {
        $handlerClass = self::handlerClass($moduleFunction);
        $fqClass = "WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler" . "\\" . $handlerClass;
        if(!class_exists($fqClass)) {
            throw new \RuntimeException("Class " . $fqClass . " not found");
        }
        $handlerObject = new $fqClass($module, $sys, $moduleConfiguration);
        if(!$handlerObject instanceof $this) {
            $handlerClass = "WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\AbstractHandler";
            throw new \RuntimeException("Class " . $fqClass . " did not produce an instance of " . $handlerClass);
        }
        return $handlerObject;
    }
    protected static function handlerClass($moduleFunction)
    {
        return str_replace(" ", "", ucwords(trim(str_replace([\WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, "_"], ["", " "], $moduleFunction))));
    }
    protected function as($handlerClass)
    {
        $newHandler = new $handlerClass($this->module, $this->systemConfiguration, $this->moduleConfiguration);
        $newHandler->log = $this->log;
        return $newHandler;
    }
    public function env() : \WHMCS\Module\Gateway\paypal_ppcpv\Environment
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\Environment::factory($this->moduleConfiguration);
    }
    public function api() : \WHMCS\Module\Gateway\paypal_ppcpv\API\Controller
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\API\Controller($this->env(), $this->log);
    }
    public function merchantStatus() : \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting
    {
        return $this->moduleConfiguration->getMerchantStatus($this->env());
    }
    public function assertEnvironmentReady() : void
    {
        $env = $this->env();
        if(!$env->hasCredentials()) {
            throw new \WHMCS\Exception\Module\NotServicable(sprintf("No PayPal account is configured for the %s environment", $env->label));
        }
    }
}

?>