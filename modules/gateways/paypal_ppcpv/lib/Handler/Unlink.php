<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class Unlink extends AbstractHandler
{
    public function handle($environment) : void
    {
        $env = $this->env();
        switch ($environment) {
            case \WHMCS\Module\Gateway\paypal_ppcpv\Environment::LIVE:
                $env->asLive($this->moduleConfiguration);
                break;
            case \WHMCS\Module\Gateway\paypal_ppcpv\Environment::SANDBOX:
                $env->asSandbox($this->moduleConfiguration);
                $this->unlink($env);
                break;
            default:
                throw new \Exception("Unknown environment " . $environment);
        }
    }
    public function unlink(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env) : \self
    {
        $api = $this->api()->withEnv($env);
        if($this->moduleConfiguration->getWebhookIdentifier($env) != "") {
            $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\DeleteWebhookRequest($api))->setWebhookId($this->moduleConfiguration->getWebhookIdentifier($env)));
        }
        $this->moduleConfiguration->unlink($env->unlink());
        $this->moduleConfiguration->persist($this->module);
        $visibleState = $this->moduleConfiguration->resolveVisibleState();
        $this->module->saveConfigValue("visible", $visibleState);
        if(!$visibleState) {
            \WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("ShowOnOrder", \WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(\DI::make("app")), \WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance())->updateVisibility(false);
        }
        $api->purgeCache();
        return $this;
    }
}

?>