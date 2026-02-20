<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class AdminAreaActions extends AbstractHandler
{
    public function __invoke() : array
    {
        if(!$this->systemConfiguration->app()->isSSLAvailable()) {
            return [];
        }
        $envLive = \WHMCS\Module\Gateway\paypal_ppcpv\Environment::factoryLive($this->moduleConfiguration);
        $envSandbox = \WHMCS\Module\Gateway\paypal_ppcpv\Environment::factorySandbox($this->moduleConfiguration);
        $actions = [$this->linkUnlinkButton($envLive), $this->linkUnlinkButton($envSandbox)];
        if(self::includeRefreshAction($this->moduleConfiguration)) {
            $actions[] = $this->refreshMerchantStatusButton();
        }
        return $actions;
    }
    public function linkUnlinkButton(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env) : array
    {
        return $env->hasCredentials() ? $this->offboardButton($env) : $this->onboardButton($env);
    }
    protected function onboardButton(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env) : array
    {
        $nonce = self::nonce();
        $prefix = ucfirst($env->label);
        $elements = OnboardingResponseHandler::viewElements();
        return ["label" => \AdminLang::trans("paypalCommerce.link" . $prefix . "Account"), "href" => $this->getLinkUri($env, $nonce), "id" => $elements->buttonOnboard($env->label), "target" => "PPFrame", "dataAttributes" => ["paypal-onboard-complete" => $elements->functionOnboard($env->label), "paypal-button" => "true", "nonce" => $nonce]];
    }
    protected function offboardButton(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env) : array
    {
        $prefix = ucfirst($env->label);
        $elements = OnboardingResponseHandler::viewElements();
        return ["label" => \AdminLang::trans("paypalCommerce.unlink" . $prefix . "Account"), "href" => sprintf("javascript:%s('%s');", $elements->functionConfirmOffboard(), $env->label), "id" => $elements->buttonOffboard($env->label)];
    }
    protected function getLinkUri(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env, string $nonce) : \WHMCS\Module\Gateway\paypal_ppcpv\Environment
    {
        $params = ["partnerId" => $env->partnerId, "product" => "PPCP", "secondaryProducts" => "advanced_vaulting,payment_methods", "integrationType" => "FO", "features" => "PAYMENT,REFUND,VAULT,BILLING_AGREEMENT,ADVANCED_TRANSACTIONS_SEARCH", "partnerClientId" => $env->partnerClientId, "displayMode" => "minibrowser", "sellerNonce" => $nonce, "capabilities" => "PAYPAL_WALLET_VAULTING_ADVANCED,GOOGLE_PAY,APPLE_PAY"];
        return sprintf("%s/bizsignup/partner/entry?%s", $env->webURL, build_query_string($params));
    }
    private static function nonce()
    {
        return (new \WHMCS\Utility\Random())->string(20, 20, 10, 0);
    }
    public function refreshMerchantStatusButton() : array
    {
        return ["label" => \AdminLang::trans("paypalCommerce.refreshAccountLabel"), "actionName" => "refresh_merchant_status", "modalSize" => "modal-md", "modal" => true];
    }
    public static function includeRefreshAction(\WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration $config) : \WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration
    {
        $live = \WHMCS\Module\Gateway\paypal_ppcpv\Environment::factoryLive($config);
        $sandbox = \WHMCS\Module\Gateway\paypal_ppcpv\Environment::factorySandbox($config);
        $refreshSettings = \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting::PAYMENTS_RECEIVABLE | \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting::EMAIL_VERIFIED | \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting::VAULTING_CAPABLE | \WHMCS\Module\Gateway\paypal_ppcpv\MerchantStatusSetting::ADVANCED_CARDS_CAPABLE;
        return $live->hasCredentials() && $config->getMerchantStatus($live)->hasnt($refreshSettings) || $sandbox->hasCredentials() && $config->getMerchantStatus($sandbox)->hasnt($refreshSettings);
    }
}

?>