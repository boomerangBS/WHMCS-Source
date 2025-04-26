<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762E7068703078376664353934323439616638_
{
}
function paypal_ppcpv_MetaData()
{
    return ["DisplayName" => WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::DISPLAY_NAME, "APIVersion" => "1.1", "VisibleDefault" => false, "gatewayType" => WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD, "noCurrencyConversion" => true];
}
function paypal_ppcpv_config()
{
    return WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance()->toConfig();
}
function paypal_ppcpv_config_validate($params)
{
    WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("ShowOnOrder", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->handle($params);
}
function paypal_ppcpv_admin_area_actions($params)
{
    return WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("paypal_ppcpv_admin_area_actions", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())();
}
function paypal_ppcpv_refresh_merchant_status($params)
{
    $envStatuses = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("merchant_status", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->updateAll();
    return ["body" => moduleView(WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, "admin.merchant_status_modal", ["module" => WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, "environmentStatuses" => $envStatuses])];
}
function paypal_ppcpv_admin_config_render($params) : WHMCS\Admin\ApplicationSupport\View\Html\GatewayConfiguration
{
    $config = WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance();
    $activeEnv = WHMCS\Module\Gateway\paypal_ppcpv\Environment::factory($config);
    $envLabelHuman = function ($e) {
        return AdminLang::trans(sprintf("paypalCommerce.labelEnvironment%s", ucfirst($e->label)));
    };
    $renderer = $params["renderer"];
    $elements = WHMCS\Module\Gateway\paypal_ppcpv\Handler\OnboardingResponseHandler::viewElements();
    $renderer->appendContainerSuffix($renderer->modal($elements->unlinkIdentifier(), AdminLang::trans("paypalCommerce.unlinkLiveAccount"), "", [["title" => AdminLang::trans("global.yes"), "id" => $elements->unlinkModalConfirm(), "onclick" => "", "class" => "btn btn-primary"], ["title" => AdminLang::trans("global.cancel")]]))->addJavascriptResource("https://www.paypal.com/webapps/merchantboarding/js/lib/lightbox/partner.js", true)->appendContainerSuffix(moduleView(WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, "js.onboarding", ["module" => WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, "elements" => WHMCS\Module\Gateway\paypal_ppcpv\Handler\OnboardingResponseHandler::viewElements()]));
    $systemConfiguration = WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app"));
    $app = $systemConfiguration->app();
    if(!$app->isSSLAvailable()) {
        $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerce.sslRequired", [":configGeneral" => "href=\"configgeneral.php\"", ":learnMore" => "href=\"https://go.whmcs.com/1705/ssl-learn-more\""]), "danger"));
    }
    foreach (WHMCS\Module\Gateway\paypal_ppcpv\Environment::eachCredentials($config) as $env) {
        $status = $config->getMerchantStatus($env);
        if(!$status->paymentsReceivable()) {
            $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerce.messageAccountLimited", [":environment" => $envLabelHuman($env)]), "danger"));
        }
        if(!$status->emailVerified()) {
            $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerce.messageVerifyEmail", [":environment" => $envLabelHuman($env)]), "danger"));
        }
        if(!$status->vaultCapable()) {
            $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerce.messageAccountNotVaultCapable", [":environment" => $envLabelHuman($env)]), "danger"));
        }
        unset($status);
    }
    unset($env);
    if(count(iterator_to_array(WHMCS\Module\Gateway\paypal_ppcpv\Environment::eachCredentials($config))) == 0) {
        $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerce.gettingStartedLinkAccount"), "info"));
    }
    $renderer->appendBodySuffix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerceAdvCards.gettingStartedCards"), "success"));
    if(WHMCS\Module\Gateway\paypal_ppcpv\Handler\AdminAreaActions::includeRefreshAction($config)) {
        $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerce.refreshAccountNotice"), "info"));
    }
    return $renderer;
}
function paypal_ppcpv_unlink(array $params)
{
    WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("paypal_ppcpv_unlink", $params["gatewayInterface"], WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->handle($params["request"]["env"] ?? "");
}
function paypal_ppcpv_post_activation($params)
{
    $cards = new WHMCS\Module\Gateway();
    if($cards->load(WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME) && !$cards->isLoadedModuleActive()) {
        WHMCS\Module\GatewaySetting::gateway(WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME)->delete();
        $cards->activate();
    }
}
function paypal_ppcpv_deactivate()
{
    $module = WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule();
    $cards = new WHMCS\Module\Gateway();
    if($cards->load(WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME) && $cards->isLoadedModuleActive()) {
        return [AdminLang::trans("paypalCommerce.deactivateUnavailableModuleAdvCards", [":module" => $module->getDisplayName(), ":sibling" => $cards->getDisplayName()])];
    }
    unset($cards);
    WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("paypal_ppcpv_deactivate", $module, WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->handle();
    return [];
}
function paypal_ppcpv_onboarding_response_handler($params)
{
    WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("paypal_ppcpv_onboarding_response_handler", $params["gatewayInterface"], WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->handle($params["request"]);
}
function paypal_ppcpv_credit_card_input($params)
{
    $handler = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("CreditCardInput", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance());
    $renderSource = $handler->assertRenderSource($params);
    if($renderSource == "admin-payment-method-add") {
        return $handler->adminAddPayment();
    }
    return $handler->handle($renderSource, $handler->resolveCurrency($renderSource, $params), $handler->resolveTotalPrice($renderSource, $params));
}
function paypal_ppcpv_remoteupdate($params)
{
    if(!isset($params["_source"])) {
        return "";
    }
    $renderSource = $params["_source"];
    $handler = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("RemoteUpdate", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance());
    if($renderSource == "admin-payment-method-edit") {
        return $handler->adminEditPaymentMethod($renderSource, $params["payMethod"]);
    }
    if($renderSource == "payment-method-edit") {
        return $handler->clientEditPaymentMethod($renderSource, $params["payMethod"]);
    }
    return "";
}
function paypal_ppcpv_capture($params)
{
    if(($params["payMethod"]->gateway_name ?? NULL) !== WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME) {
        throw new Exception("Invoice payment method does not match module gateway");
    }
    $module = WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule();
    $paypalToken = WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController::factoryModule($module)->tokenFromPayMethod($params["payMethod"]);
    if(is_null($paypalToken)) {
        throw new Exception("Invoice payment method token not found");
    }
    $captureHandler = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("paypal_ppcpv_capture", $module, WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance());
    $invoice = $captureHandler->loadInvoice($params["invoiceid"]);
    if(is_null($invoice)) {
        return $captureHandler->errorInvalidInvoice($params["invoiceid"])->get();
    }
    $paymentSource = (new WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedPaypalPaymentSource())->withVaultedToken($paypalToken);
    return $captureHandler->handle($invoice, $paymentSource)->get();
}
function paypal_ppcpv_post_checkout(array $params = [])
{
    $postCheckoutHandler = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("PostCheckout", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance());
    try {
        $invoice = WHMCS\Billing\Invoice::findOrFail((int) $params["invoiceid"]);
        $orderId = WHMCS\Module\Gateway\paypal_ppcpv\Util::getAndDeleteSession("remoteStorageToken", "oi-");
        if(!$invoice->requiresPayment()) {
            return NULL;
        }
        if(WHMCS\Session::get("orderdetails")["ccinfo"] == "new") {
            return $postCheckoutHandler->captureOrder($orderId, $invoice)->get();
        }
        return $postCheckoutHandler->captureInvoice($invoice)->get();
    } catch (WHMCS\Exception\Gateways\RedirectToInvoice $e) {
        $_SESSION["orderdetails"]["paymentcomplete"] = true;
        throw $e;
    } catch (Exception $e) {
        throw new WHMCS\Exception\Gateways\RedirectToInvoice();
    }
}
function paypal_ppcpv_cc_validation($params)
{
    $cartTotal = WHMCS\Cart\CartCalculator::fromSession()->getTotal()->getValue();
    if(valueIsZero($cartTotal)) {
        return "";
    }
    if($params["cardnew"] && !WHMCS\Session::exists("remoteStorageToken")) {
        return Lang::trans("paypalCommerce.error.noAccount");
    }
    return "";
}
function paypal_ppcpv_storeremote($params)
{
    $module = WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule();
    switch ($params["action"]) {
        case "delete":
            return WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("PaymentToken", $module, WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->delete(WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController::factoryModule($module)->tokenFromPayMethod($params["payMethod"]));
            break;
        case "update":
            return ["status" => "success"];
            break;
        case "create":
            PaymentToken();
            $storeRemoteResult = $easytoyou_error_decompile;
            $result = new $storeRemoteResult();
            try {
                $setupToken = WHMCS\Module\Gateway\paypal_ppcpv\Util::getAndDeleteSession("remoteStorageToken", "st-");
                $result->setStatus("success");
                if(0 < strlen($setupToken)) {
                    $createPaymentTokenResponse = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("PaymentHandler", $module, WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->createPaymentToken((new WHMCS\Module\Gateway\paypal_acdc\API\Entity\SetupTokenPaymentSource())->setIdentifier($setupToken));
                    $vaultToken = WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken::factory($createPaymentTokenResponse->getCustomerIdentifier(), $createPaymentTokenResponse->getVaultTokenIdentifier(), NULL, $createPaymentTokenResponse->getPaymentSource());
                    $result->setGatewayId($vaultToken->transformToTokenJSON())->setPayPalDetail($vaultToken)->setRawData(json_decode($createPaymentTokenResponse->pack(), true));
                }
                return $result->get();
            } catch (Exception $e) {
                return $result->setStatus("error")->setRawData($e->getMessage())->get();
            }
            break;
        default:
            return [];
    }
}
function paypal_ppcpv_refund($params)
{
    $refundHandler = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("paypal_ppcpv_refund", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance());
    try {
        return $refundHandler->handle($params["invoiceid"], $params["transid"], (string) WHMCS\View\Formatter\Price::adjustDecimals($params["amount"], $params["currency"]), $params["currency"]);
    } catch (Exception $e) {
        return $refundHandler->createErrorArray($params["transid"], $e->getMessage());
    }
}
function paypal_ppcpv_ListDisputes()
{
    return WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("DisputeHandler", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->listDisputes();
}
function paypal_ppcpv_FetchDispute($params) : WHMCS\Billing\Payment\Dispute
{
    return WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("DisputeHandler", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->fetchDispute($params["disputeId"]);
}
function paypal_ppcpv_CloseDispute($params)
{
    WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("DisputeHandler", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance())->closeDispute($params["disputeId"]);
}
function paypal_ppcpv_account_balance($params) : WHMCS\Module\Gateway\BalanceCollection
{
    $handler = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("MerchantAccount", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance());
    $handler->assertEnvironmentReady();
    return $handler->getBalances();
}
function paypal_ppcpv_TransactionInformation($params) : WHMCS\Billing\Payment\Transaction\Information
{
    $handler = WHMCS\Module\Gateway\paypal_ppcpv\Handler\AbstractHandler::factory("Transaction", WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::fromPersistance());
    return $handler->getTransaction($params["transactionId"]);
}

?>