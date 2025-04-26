<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\Handler;

class CreditCardInput extends AbstractHandler
{
    public function handle($renderSource, $currency, $totalPrice) : \WHMCS\Billing\Currency
    {
        $module = \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME;
        return moduleView("paypal_acdc", "js.js-sdk", ["renderSource" => $renderSource, "module" => $module, "paypalSDK" => (new \WHMCS\Module\Gateway\paypal_ppcpv\JSSDK($this->env()))->withCurrency($currency->code), "routeCreateOrder" => routePathWithQuery($module . "-create-order"), "routeInvoiceOnApprove" => routePathWithQuery($module . "-invoice-on-approve"), "routeCreateSetupToken" => routePathWithQuery($module . "-create-setup-token"), "routeCreatePaymentToken" => routePathWithQuery($module . "-create-payment-token"), "routePaymentMethods" => routePathWithQuery("account-paymentmethods"), "requiresPayment" => $renderSource == "checkout" ? 0 < $totalPrice : NULL, "showSaveToggle" => $this->isSaveAvailable()]);
    }
    private function isSaveAvailable()
    {
        return \WHMCS\Config\Setting::getValue("CCAllowCustomerDelete") && $this->isMerchantVaultCapable();
    }
    public function isMerchantVaultCapable()
    {
        return $this->moduleConfiguration->getMerchantStatus($this->env())->vaultCapable();
    }
    public function adminAddPayment()
    {
        $module = \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME;
        $description = \AdminLang::trans("paypalCommerce.addNotSupported");
        return "<div id=\"" . $module . "-admin-add-payment\">\n" . $description . "\n</div>\n<script type=\"application/javascript\">\njQuery(document).ready(function() {\n    jQuery('#btnSave').hide();\n    jQuery('#frmCreditCardDetails').hide()\n        .before(jQuery('#" . $module . "-admin-add-payment'));\n});\n</script>" . \WHMCS\View\Asset::jsInclude("jquery.payment.js");
    }
}

?>