<?php

class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F616364632E7068703078376664353934323438656462_
{
}
function paypal_acdc_MetaData()
{
    return ["DisplayName" => WHMCS\Module\Gateway\paypal_acdc\Core::DISPLAY_NAME, "APIVersion" => "1.1", "VisibleDefault" => false, "gatewayType" => WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD, "noCurrencyConversion" => true];
}
function paypal_acdc_config()
{
    return ["FriendlyName" => ["Type" => "System", "Value" => WHMCS\Module\Gateway\paypal_acdc\Core::DISPLAY_NAME]];
}
function paypal_acdc_config_validate($params)
{
    WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("ShowOnOrder", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance())->handle($params);
}
function paypal_acdc_post_activation($params)
{
    $commerce = new WHMCS\Module\Gateway();
    if($commerce->load(WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME) && !$commerce->isLoadedModuleActive()) {
        WHMCS\Module\GatewaySetting::gateway(WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME)->delete();
        $commerce->activate();
    }
}
function paypal_acdc_admin_config_render($params) : WHMCS\Admin\ApplicationSupport\View\Html\GatewayConfiguration
{
    $renderer = $params["renderer"];
    $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerceAdvCards.infoSharedSettings", [":module" => WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::loadModule()->getDisplayName()]), "info"));
    $config = WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance();
    $env = WHMCS\Module\Gateway\paypal_ppcpv\Environment::factory($config);
    if($env->hasCredentials()) {
        $status = $config->getMerchantStatus($env);
        if(!$status->cardsCapable()) {
            $renderer->appendBodyPrefix(WHMCS\View\Helper::alert(AdminLang::trans("paypalCommerceAdvCards.missingCapability"), "warning"));
        }
    }
    return $renderer;
}
function paypal_acdc_capture($params)
{
    if(!isset($params["payMethod"])) {
        throw new InvalidArgumentException("Required value \$params['payMethod'] missing");
    }
    $handler = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("paypal_acdc_capture", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
    try {
        return $handler->captureInvoice((int) $params["invoiceid"], $params["payMethod"])->get();
    } catch (Exception $e) {
        return ["status" => "error", "rawdata" => [], "declinereason" => "incomplete"];
    }
}
function paypal_acdc_post_checkout(array $params = [])
{
    $handler = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("paypal_acdc_post_checkout", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
    try {
        $invoice = WHMCS\Billing\Invoice::findOrFail((int) $params["invoiceid"]);
        $orderId = WHMCS\Module\Gateway\paypal_ppcpv\Util::getAndDeleteSession("remoteStorageToken", "oi-");
        if(!$invoice->requiresPayment()) {
            return NULL;
        }
        if(WHMCS\Session::get("orderdetails")["ccinfo"] == "new") {
            return $handler->captureOrder($orderId, $invoice)->get();
        }
        $captureResult = $handler->captureInvoice($invoice);
        if($captureResult->is3DSRequired()) {
            return NULL;
        }
        return $captureResult->get();
    } catch (WHMCS\Exception\Gateways\RedirectToInvoice $e) {
        $_SESSION["orderdetails"]["paymentcomplete"] = true;
        throw $e;
    } catch (Exception $e) {
        throw new WHMCS\Exception\Gateways\RedirectToInvoice();
    }
}
function paypal_acdc_cc_validation(array $params = [])
{
    return "";
}
function paypal_acdc_credit_card_input(array $params = [])
{
    $handler = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("paypal_acdc_credit_card_input", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
    $renderSource = WHMCS\Module\Gateway\paypal_ppcpv\Handler\CreditCardInput::assertRenderSource($params);
    if($renderSource == "admin-payment-method-add") {
        return $handler->adminAddPayment();
    }
    return $handler->handle($renderSource, WHMCS\Module\Gateway\paypal_ppcpv\Handler\CreditCardInput::resolveCurrency($renderSource, $params), WHMCS\Module\Gateway\paypal_ppcpv\Handler\CreditCardInput::resolveTotalPrice($renderSource, $params));
}
function paypal_acdc_storeremote($params)
{
    $module = WHMCS\Module\Gateway\paypal_acdc\Core::loadModule();
    switch ($params["action"]) {
        case "delete":
            return WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::extensionFactory("PaymentToken", $module, WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance())->delete(WHMCS\Module\Gateway\paypal_acdc\VaultTokenController::factoryModule($module)->tokenFromPayMethod($params["payMethod"]));
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
                    $createPaymentTokenResponse = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("PaymentHandler", $module, WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance())->createPaymentToken((new WHMCS\Module\Gateway\paypal_acdc\API\Entity\SetupTokenPaymentSource())->setIdentifier($setupToken));
                    $vaultToken = WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken::factory($createPaymentTokenResponse->getCustomerIdentifier(), $createPaymentTokenResponse->getVaultTokenIdentifier(), NULL, $createPaymentTokenResponse->getPaymentSource());
                    $expiry = $vaultToken->cardExpiryCarbon();
                    $result->setGatewayId($vaultToken->transformToTokenJSON())->setCardDetail($vaultToken->cardHint(), $expiry !== false ? $expiry->format("m/Y") : "", $vaultToken->brand())->setRawData(json_decode($createPaymentTokenResponse->pack(), true));
                    unset($expiry);
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
function paypal_acdc_refund($params)
{
    $refundHandler = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::extensionFactory("Refund", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
    try {
        return $refundHandler->handle($params["invoiceid"], $params["transid"], (string) WHMCS\View\Formatter\Price::adjustDecimals($params["amount"], $params["currency"]), $params["currency"]);
    } catch (Exception $e) {
        return $refundHandler->createErrorArray($params["transid"], $e->getMessage());
    }
}
function paypal_acdc_3dsecure($params)
{
    if(!isset($params["payMethod"])) {
        throw new InvalidArgumentException("Required value \$params['payMethod'] missing");
    }
    try {
        $threeDSecureHandler = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("paypal_acdc_three_d_secure", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
        $threeDSOrderIdentifier = $threeDSecureHandler->getAndDeleteSessionPayPalThreeDSCheckout();
        if(0 < strlen($threeDSOrderIdentifier)) {
            return $threeDSecureHandler->byOrderIdentifier($threeDSOrderIdentifier);
        }
        return $threeDSecureHandler->byInvoiceIdentifier($params["invoiceid"], $params["payMethod"]);
    } catch (Exception $e) {
        return "declined";
    }
}
function paypal_acdc_storesupported()
{
    return WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::factory("CreditCardInput", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance())->isMerchantVaultCapable();
}
function paypal_acdc_TransactionInformation($params) : WHMCS\Billing\Payment\Transaction\Information
{
    $handler = WHMCS\Module\Gateway\paypal_acdc\Handler\AbstractHandler::extensionFactory("Transaction", WHMCS\Module\Gateway\paypal_acdc\Core::loadModule(), WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(DI::make("app")), WHMCS\Module\Gateway\paypal_acdc\ModuleConfiguration::fromPersistance());
    return $handler->getTransaction($params["transactionId"]);
}

?>