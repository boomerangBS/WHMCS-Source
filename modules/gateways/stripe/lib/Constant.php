<?php


namespace WHMCS\Module\Gateway\Stripe;
class Constant
{
    public static $appPartnerId = "pp_partner_EzZDHkkVgTrj6n";
    public static $apiVersion = "2023-10-16";
    public static $appName = "WHMCS";
    public static $appUrl = "https://www.whmcs.com";
    const RAK_APP_URL = "https://go.whmcs.com/1841/stripe-app";
    const STRIPE_CURRENCIES_NO_DECIMALS = ["BIF", "CLP", "DJF", "GNF", "JPY", "KMF", "KRW", "MGA", "PYG", "RWF", "VND", "VUV", "XAF", "XOF", "XPF"];
    const LIVE_SECRET_KEY_PATTERN = "/^[sr]k_live_/";
}

?>