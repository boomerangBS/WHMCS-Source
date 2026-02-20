<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class MerchantStatus extends AbstractHandler
{
    public function updateAll() : \SplObjectStorage
    {
        $environmentsStatus = new \SplObjectStorage();
        foreach ($this->checkAll() as $env => $response) {
            $environmentsStatus->attach($env, $this->update($env, $response));
        }
        return $environmentsStatus;
    }
    public function update(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env = NULL, $response) : \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting
    {
        if(is_null($response)) {
            $response = $this->check($env);
        }
        $status = \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting::make();
        if($response instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\MerchantStatusResponse) {
            $this->persistStatus($env, $response);
            $this->logStatus($env, $response);
            $status->fromResponse($response);
        } else {
            $this->logCheckError($env, $response);
        }
        return $status;
    }
    public function checkAll() : \Generator
    {
        foreach (\WHMCS\Module\Gateway\paypal_ppcpv\Environment::eachCredentials($this->moduleConfiguration) as $env) {
            yield $this->check($env);
        }
    }
    public function check(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse
    {
        $api = $this->api()->withEnv($env);
        return $api->send(new \WHMCS\Module\Gateway\paypal_ppcpv\API\MerchantStatusRequest($api));
    }
    public function persistStatus(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env, $remoteStatus) : void
    {
        $this->moduleConfiguration->setMerchantStatus($env, \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting::make()->fromResponse($remoteStatus));
        $this->moduleConfiguration->persist($this->module);
    }
    public function logStatus(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env, $remoteStatus) : void
    {
        $envLabelHuman = \AdminLang::trans(sprintf("paypalCommerce.labelEnvironment%s", ucfirst($env->label)));
        if(!$remoteStatus->paymentsReceivable()) {
            $this->log->activity(\AdminLang::trans("paypalCommerce.messageAccountLimited", [":environment" => $envLabelHuman]));
        }
        if(!$remoteStatus->primaryEmailConfirmed()) {
            $this->log->activity(\AdminLang::trans("paypalCommerce.messageVerifyEmail", [":environment" => $envLabelHuman]));
        }
    }
    protected function logCheckError(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env, \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse $r)
    {
        $envLabelHuman = \AdminLang::trans(sprintf("paypalCommerce.labelEnvironment%s", ucfirst($env->label)));
        $this->log->activity(sprintf("[%s] An error occurred while attempting to refresh the account status. (%s)", $envLabelHuman, $r->__toString()));
    }
}

?>