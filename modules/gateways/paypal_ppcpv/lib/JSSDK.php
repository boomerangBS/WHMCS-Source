<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class JSSDK
{
    protected $env;
    protected $currencyCode;
    public function __construct(Environment $env)
    {
        $this->env = $env;
    }
    public function withCurrency($currencyCode) : \self
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }
    public function options() : array
    {
        $sdkOptions = ["components" => "buttons,card-fields", "client-id" => $this->env->clientId, "integration-date" => $this->env->integrationDate, "disable-funding" => "paylater,credit", "intent" => "capture"];
        if($this->currencyCode != "") {
            $sdkOptions["currency"] = $this->currencyCode;
        }
        return $sdkOptions;
    }
    public function renderUrl()
    {
        return sprintf("https://www.paypal.com/sdk/js?%s", build_query_string($this->options()));
    }
    public function renderTag()
    {
        return sprintf("<script src=\"%s\" data-partner-attribution-id=\"%s\"></script>", $this->renderUrl(), $this->env->partnerId);
    }
    public function renderTagAsScriptElement()
    {
        $url = addslashes($this->renderUrl());
        $partnerId = addslashes($this->env->partnerId);
        return "function (doc, elementId) {\n    node = doc.createElement('script');\n    node.setAttribute('id', elementId);\n    node.setAttribute('data-partner-attribution-id', '" . $partnerId . "');\n    node.setAttribute('async', 'true');\n    node.setAttribute('src', '" . $url . "');\n    return node;\n}";
    }
}

?>