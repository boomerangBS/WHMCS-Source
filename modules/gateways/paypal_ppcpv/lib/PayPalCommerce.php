<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv;
class PayPalCommerce
{
    const MODULE_NAME = "paypal_ppcpv";
    const DISPLAY_NAME = "PayPal";
    const WEB_URL_LIVE = "https://www.paypal.com";
    const WEB_URL_SANDBOX = "https://www.sandbox.paypal.com";
    const API_URL_LIVE = "https://api-m.paypal.com";
    const API_URL_SANDBOX = "https://api-m.sandbox.paypal.com";
    const INTEGRATION_DATE = "2023-11-28";
    const PARTNER_ATTRIBUTION_ID = "WHMCS_Ecom_PPCPV2";
    const LIVE_PARTNER_ID = "HA8JRU89P2JUL";
    const SANDBOX_PARTNER_ID = "HW5N6KWS3CM38";
    const LIVE_PARTNER_CLIENT_ID = "AceAOmUYe0WfqM1fyucKNprMrAzIMxdcMG8iKUAoWnWfU_mM4U_Ve_n-djFS1wHFOqkBCdFBhp07uaS6";
    const SANDBOX_PARTNER_CLIENT_ID = "Aaeo7eqaGlpmlsXeEV3XA60eVrwe6VITzGOX-euhbPndaIHApT2UJinFdtpwaPdzE7aGJuFlRu6x-g95";
    public static function loadModule() : \WHMCS\Module\Gateway
    {
        $module = new \WHMCS\Module\Gateway();
        if(!$module->load(static::MODULE_NAME)) {
            throw new \Exception(sprintf("Failed to load module '%s'", static::DISPLAY_NAME));
        }
        return $module;
    }
    public static function transientDataPrefix()
    {
        return "gateway-" . static::MODULE_NAME;
    }
    public static function callbackPath()
    {
        return "modules/gateways/callback/paypal_ppcpv.php";
    }
}

?>