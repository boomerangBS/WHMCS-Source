<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class Environment
{
    public $label = "";
    public $clientId = "";
    public $clientSecret = "";
    public $payerId = "";
    public $partnerId = "";
    public $partnerClientId = "";
    public $attributionId = "";
    public $apiURL = "";
    public $webURL = "";
    public $integrationDate = "";
    const LIVE = "live";
    const SANDBOX = "sandbox";
    public static function instance() : \self
    {
        $o = new self();
        $o->attributionId = PayPalCommerce::PARTNER_ATTRIBUTION_ID;
        $o->integrationDate = PayPalCommerce::INTEGRATION_DATE;
        return $o;
    }
    public static function factory(ModuleConfiguration $c) : \self
    {
        if($c->useSandbox) {
            return self::factorySandbox($c);
        }
        return self::factoryLive($c);
    }
    public static function factoryLive(ModuleConfiguration $c) : \self
    {
        return self::instance()->asLive($c);
    }
    public function asLive(ModuleConfiguration $c) : \self
    {
        $this->label = self::LIVE;
        $this->clientId = $c->clientId;
        $this->clientSecret = $c->clientSecret;
        $this->payerId = $c->payerId;
        $this->partnerClientId = PayPalCommerce::LIVE_PARTNER_CLIENT_ID;
        $this->partnerId = PayPalCommerce::LIVE_PARTNER_ID;
        $this->apiURL = PayPalCommerce::API_URL_LIVE;
        $this->webURL = PayPalCommerce::WEB_URL_LIVE;
        return $this;
    }
    public static function factorySandbox(ModuleConfiguration $c) : \self
    {
        return self::instance()->asSandbox($c);
    }
    public function asSandbox(ModuleConfiguration $c) : \self
    {
        $this->label = self::SANDBOX;
        $this->clientId = $c->sandboxClientId;
        $this->clientSecret = $c->sandboxClientSecret;
        $this->payerId = $c->sandboxPayerId;
        $this->partnerId = PayPalCommerce::SANDBOX_PARTNER_ID;
        $this->partnerClientId = PayPalCommerce::SANDBOX_PARTNER_CLIENT_ID;
        $this->apiURL = PayPalCommerce::API_URL_SANDBOX;
        $this->webURL = PayPalCommerce::WEB_URL_SANDBOX;
        return $this;
    }
    public function unlink() : \self
    {
        $this->clientId = "";
        $this->clientSecret = "";
        $this->payerId = "";
        return $this;
    }
    public function link($clientId, string $clientSecret, string $payerid) : \self
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->payerId = $payerid;
        return $this;
    }
    public static function each(ModuleConfiguration $c) : \Generator
    {
        yield static::factorySandbox($c);
        yield static::factoryLive($c);
    }
    public static function eachCredentials(ModuleConfiguration $c) : \Generator
    {
        $env = static::factorySandbox($c);
        if($env->hasCredentials()) {
            yield $env;
        }
        $env = static::factoryLive($c);
        if($env->hasCredentials()) {
            yield $env;
        }
    }
    public function hasCredentials()
    {
        return $this->clientSecret != "" && $this->clientId != "";
    }
}

?>