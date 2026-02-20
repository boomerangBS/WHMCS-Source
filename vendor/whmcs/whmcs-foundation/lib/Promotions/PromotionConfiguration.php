<?php

namespace WHMCS\Promotions;

class PromotionConfiguration
{
    public function promotions() : array
    {
        return [["identifier" => "PayPalPayments", "class" => "WHMCS\\Promotions\\GatewayPromotion", "classConstructorArguments" => ["paypal_ppcpv", ["paypal", "paypalcheckout", "paypalpaymentspro", "paypalpaymentsproref"]], "title" => "promotions.payPalPayments.title", "description" => "promotions.payPalPayments.description", "action" => (new PromotionAction(routePath("admin-apps-info", "gateways.paypal_ppcpv"), "promotions.payPalPayments.actionText"))->asModal(), "logoUrl" => "images/icons/paypal-logo.png", "dismissTTL" => 4838400], ["identifier" => "LegacyPayPal", "class" => "WHMCS\\Promotions\\SupersedingGatewayPromotion", "classConstructorArguments" => ["paypal_ppcpv"], "title" => "promotions.payPalLegacy.title", "description" => "promotions.payPalLegacy.description", "action" => (new PromotionAction(routePath("admin-apps-info", "gateways.paypal_ppcpv"), "promotions.payPalPayments.actionText"))->asModal(), "logoUrl" => "images/icons/paypal-logo.png", "dismissTTL" => 4838400], ["identifier" => "Sitejet", "class" => "WHMCS\\Promotions\\SitejetPromotion", "title" => "utilities.sitejetBuilder.prompt.title", "description" => "utilities.sitejetBuilder.prompt.description", "action" => new PromotionAction("utilities/sitejet/builder", "utilities.sitejetBuilder.prompt.activateButton"), "logoUrl" => "images/icons/sitejet-logo.png", "dismissTTL" => 7776000]];
    }
    public function promotionByIdentifier($identifier) : array
    {
        foreach ($this->promotions() as $promotion) {
            if($identifier === $promotion["identifier"]) {
                return $promotion;
            }
        }
        throw new \InvalidArgumentException("'" . $identifier . "' is not a valid promotion identifier");
    }
}

?>