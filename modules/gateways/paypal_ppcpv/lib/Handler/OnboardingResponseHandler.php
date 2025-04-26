<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class OnboardingResponseHandler extends AbstractHandler
{
    private static $eventTypes = ["PAYMENT.CAPTURE.COMPLETED", "PAYMENT.CAPTURE.DENIED", "PAYMENT.CAPTURE.DECLINED", "PAYMENT.CAPTURE.REFUNDED", "PAYMENT.CAPTURE.REVERSED", "PAYMENT.CAPTURE.PENDING", "VAULT.PAYMENT-TOKEN.CREATED", "VAULT.PAYMENT-TOKEN.DELETED"];
    public static function viewElements()
    {
        return new func_num_args();
    }
    public function handle(\WHMCS\Http\Message\ServerRequest $request) : void
    {
        $env = $this->env();
        $request->get("env");
        switch ($request->get("env")) {
            case \WHMCS\Module\Gateway\paypal_ppcpv\Environment::LIVE:
                $env->asLive($this->moduleConfiguration);
                break;
            case \WHMCS\Module\Gateway\paypal_ppcpv\Environment::SANDBOX:
                $env->asSandbox($this->moduleConfiguration);
                $api = $this->api()->withEnv($env);
                $credentials = $this->fetchCredentials($api, $request->get("nonce"), $request->get("authCode"), $request->get("sharedId"));
                $env->link($credentials->clientId(), $credentials->clientSecret(), $credentials->payerId());
                $this->moduleConfiguration->withEnvironmentCredentials($env);
                $api = $this->api()->withEnv($env);
                $webhook = $this->createWebhooks($api, self::$eventTypes);
                if($webhook instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateWebhookResponse) {
                    $this->moduleConfiguration->setWebhookIdentifier($env, $webhook->id());
                }
                $this->moduleConfiguration->persist($this->module);
                AbstractHandler::factory("merchant_status", $this->module, $this->systemConfiguration, $this->moduleConfiguration)->update($env);
                break;
            default:
                throw new \Exception("Unknown environment " . $request->get("env"));
        }
    }
    private function fetchCredentials(\WHMCS\Module\Gateway\paypal_ppcpv\API\Controller $api, string $sellerNonce, string $authCode, string $sharedId) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse
    {
        $response = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\SellerAccessTokenRequest($api))->setSellerNonce($sellerNonce)->setAuthCode($authCode)->setSharedIdentifier($sharedId));
        if(!$response instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\SellerAccessTokenResponse) {
            throw new \Exception($response->__toString());
        }
        return $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\SellerCredentialsRequest($api))->setToken($response->token()));
    }
    private function createWebhooks(\WHMCS\Module\Gateway\paypal_ppcpv\API\Controller $api, array $eventTypes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse
    {
        return $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateWebhookRequest($api))->setEventsTypes($eventTypes)->setUrl(sprintf("%s/%s", $this->systemConfiguration->app()->getSystemURL(false), \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::callbackPath())));
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F48616E646C65722F4F6E626F617264696E67526573706F6E736548616E646C65722E7068703078376664353934323439316639_
{
    protected function buttonBoard($action, string $envLabel)
    {
        return sprintf("%s-%s-%s", \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, $action, $envLabel);
    }
    public function buttonOnboard($envLabel)
    {
        return $this->buttonBoard("onboard", $envLabel);
    }
    public function buttonOnboardLive()
    {
        return $this->buttonOnboard(\WHMCS\Module\Gateway\paypal_ppcpv\Environment::LIVE);
    }
    public function buttonOnboardSandbox()
    {
        return $this->buttonOnboard(\WHMCS\Module\Gateway\paypal_ppcpv\Environment::SANDBOX);
    }
    public function buttonOffboard($envLabel)
    {
        return $this->buttonBoard("offboard", $envLabel);
    }
    public function buttonOffboardLive()
    {
        return $this->buttonOffboard(\WHMCS\Module\Gateway\paypal_ppcpv\Environment::LIVE);
    }
    public function buttonOffboardSandbox()
    {
        return $this->buttonOffboard(\WHMCS\Module\Gateway\paypal_ppcpv\Environment::SANDBOX);
    }
    public function functionOnboard($envLabel)
    {
        return sprintf("%s_onboardingComplete%s", \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, ucfirst($envLabel));
    }
    public function functionOffboard()
    {
        return sprintf("%s.unlinkAccount", \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME);
    }
    public function functionConfirmOffboard()
    {
        return sprintf("%s.confirmUnlink", \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME);
    }
    public function unlinkIdentifier()
    {
        return sprintf("%s-unlink-account", \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME);
    }
    public function unlinkModal()
    {
        return "modal" . $this->unlinkIdentifier();
    }
    public function unlinkModalConfirm()
    {
        return sprintf("%s-Yes", $this->unlinkIdentifier());
    }
}

?>