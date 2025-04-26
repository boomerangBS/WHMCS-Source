<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class CreditCardInput extends AbstractHandler
{
    public function handle($renderSource, $currency, $totalPrice) : \WHMCS\Billing\Currency
    {
        $module = \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
        return moduleView($module, "js.smart_buttons-sdk", ["paypalSDK" => (new \WHMCS\Module\Gateway\paypal_ppcpv\JSSDK($this->env()))->withCurrency($currency->code), "renderSource" => $renderSource, "module" => $module, "routeCreateOrder" => routePathWithQuery($module . "-create-order"), "routeOnApprove" => routePathWithQuery($module . "-on-approve"), "routeCreateSetupToken" => routePathWithQuery($module . "-create-setup-token"), "routeGetSetupToken" => routePathWithQuery($module . "-get-setup-token"), "requiresPayment" => $renderSource == "checkout" ? 0 < $totalPrice : true]);
    }
    public static function assertRenderSource($params)
    {
        if(!isset($params["_source"]) || strlen($params["_source"]) == 0) {
            throw new \RuntimeException("Unknown calling _source for credit_card_input");
        }
        return $params["_source"];
    }
    public static function resolveTotalPrice($renderSource, array $params) : array
    {
        $total = 0;
        switch ($renderSource) {
            case "checkout":
                $total = $params["total"]->getValue();
                break;
            case "invoice-pay":
                $assertInvoice = function ($invoiceRef) {
                    if(is_null($invoiceRef)) {
                        throw new \RuntimeException("Unknown invoice");
                    }
                    return $invoiceRef;
                };
                $total = $assertInvoice(\WHMCS\Billing\Invoice::find($assertInvoice($params["invoiceid"])))->cart()->getTotal()->toNumeric();
                unset($assertInvoice);
                break;
            case "payment-method-add":
            case "admin-payment-method-add":
                return $total;
                break;
            default:
                throw new \RuntimeException(sprintf("Unable to determine total price relevant to context %s", $renderSource));
        }
    }
    public static function resolveCurrency($renderSource, array $params) : \WHMCS\Billing\Currency
    {
        $currency = NULL;
        switch ($renderSource) {
            case "checkout":
                $currency = $params["total"]->getCurrency();
                break;
            case "invoice-pay":
                $assertInvoice = function ($invoiceRef) {
                    if(is_null($invoiceRef)) {
                        throw new \RuntimeException("Unknown invoice");
                    }
                    return $invoiceRef;
                };
                $currency = $assertInvoice(\WHMCS\Billing\Invoice::find($assertInvoice($params["invoiceid"])))->getCurrencyModel();
                unset($assertInvoice);
                break;
            case "payment-method-add":
            case "admin-payment-method-add":
                $currency = \WHMCS\Billing\Currency::defaultCurrency()->first();
                return $currency;
                break;
            default:
                throw new \RuntimeException(sprintf("Unable to determine currency relevant to context %s", $renderSource));
        }
    }
    public function adminAddPayment()
    {
        $module = \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME;
        $description = \AdminLang::trans("paypalCommerce.addNotSupported");
        return "<div id=\"" . $module . "-admin-add-payment\">\n" . $description . "\n</div>\n<script type=\"application/javascript\">\njQuery(document).ready(function() {\n    jQuery('#btnSave').hide();\n    jQuery('#frmCreditCardDetails').hide()\n        .before(jQuery('#" . $module . "-admin-add-payment'));\n});\n</script>" . \WHMCS\View\Asset::jsInclude("jquery.payment.js");
    }
}

?>