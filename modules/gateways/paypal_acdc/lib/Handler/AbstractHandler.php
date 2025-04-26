<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\Handler;

abstract class AbstractHandler extends \WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler
{
    protected $moduleConfiguration;
    protected $log;
    public function __construct(\WHMCS\Module\Gateway $module, \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration $sys, \WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration $config)
    {
        $this->module = $module;
        $this->systemConfiguration = $sys;
        $this->withModuleConfiguration($config);
        $this->log = \WHMCS\Module\Gateway\paypal_acdc\Logger::factory($config, $module);
    }
    public static function factory($moduleFunction, $module, $sys, $moduleConfiguration) : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler
    {
        $handlerClass = self::handlerClass($moduleFunction);
        $fqClass = "WHMCS\\Module\\Gateway\\paypal_acdc\\Handler" . "\\" . $handlerClass;
        if(!class_exists($fqClass)) {
            throw new \RuntimeException("Class " . $fqClass . " not found");
        }
        $handlerObject = new $fqClass($module, $sys, $moduleConfiguration);
        if(!$handlerObject instanceof $this) {
            $handlerClass = "WHMCS\\Module\\Gateway\\paypal_acdc\\Handler\\AbstractHandler";
            throw new \RuntimeException("Class " . $fqClass . " did not produce an instance of " . $handlerClass);
        }
        return $handlerObject;
    }
    public static function extensionFactory($extensionHandlerClass, $module, $sys, $moduleConfiguration) : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler
    {
        $extensionNamespace = (new \ReflectionClass("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\AbstractHandler"))->getNamespaceName();
        $extensionFQClass = $extensionNamespace . "\\" . $extensionHandlerClass;
        $handlerObject = new ExtendCommerce($module, $sys, $moduleConfiguration);
        $handlerObject = $handlerObject->as($extensionFQClass);
        if(!$handlerObject instanceof \WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler) {
            $handlerClass = "WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\AbstractHandler";
            throw new \RuntimeException("Class " . $extensionFQClass . " did not produce an instance of " . $handlerClass);
        }
        return $handlerObject;
    }
    public function asExtension($handlerClass) : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler
    {
        return self::extensionFactory($handlerClass, \WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(\DI::make("app")), \WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
    }
    protected static function handlerClass($moduleFunction)
    {
        return str_replace(" ", "", ucwords(trim(str_replace([\WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME, "_"], ["", " "], $moduleFunction))));
    }
    public function withModuleConfiguration(\WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration $config)
    {
        if(!$config instanceof \WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration) {
            throw new \RuntimeException(sprintf("Expecting type %s", "WHMCS\\Module\\Gateway\\paypal_acdc\\ModuleConfiguration"));
        }
        $this->moduleConfiguration = $config;
        return $this;
    }
    public function api() : \WHMCS\Module\Gateway\paypal_ppcpv\API\Controller
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\API\Controller($this->env(), $this->log);
    }
}

?>