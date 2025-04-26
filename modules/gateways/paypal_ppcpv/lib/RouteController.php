<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class RouteController
{
    public function unlink(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            Handler\AbstractHandler::factory("unlink", PayPalCommerce::loadModule(), SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->handle($request->get("env", ""));
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["success" => false]);
        }
        $adminBaseUrl = \App::getSystemURL() . \App::get_admin_folder_name();
        return new \WHMCS\Http\Message\JsonResponse(["success" => true, "redirectUrl" => sprintf("%s/configgateways.php?updated=%2\$s#%2\$s", $adminBaseUrl, PayPalCommerce::MODULE_NAME)]);
    }
    public function createOrder(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            $requestValues = $this->parseJson((string) $request->getBody());
            $this->assertExpectedProperties($requestValues, ["token"]);
            check_token("WHMCS.default", $requestValues->token);
            $client = \Auth::client();
            $invoice = NULL;
            if(!empty($requestValues->invoiceid)) {
                $invoice = \WHMCS\Billing\Invoice::findOrFail($requestValues->invoiceid);
                if($invoice->clientId != $client->id) {
                    throw new \WHMCS\Exception\Authorization\AccessDenied("Unauthorized");
                }
                $invoice->clearPayMethodId()->save();
            }
            $cartCalculatorModel = !is_null($invoice) ? $invoice->cart() : \WHMCS\Cart\CartCalculator::fromSession();
            $returnUrl = \App::getSystemURL() . "viewinvoice.php?id=0";
            $paymentSource = $this->paymentSourceFromOrderForm($returnUrl, $returnUrl . "&paymentfailed=true", \WHMCS\Config\Setting::getValue("CompanyName"), true)->setExperiencePaymentMethodPreference("IMMEDIATE_PAYMENT_REQUIRED")->setExperiencePaymentMethodSelected("PAYPAL")->setExperienceUserAction("PAY_NOW");
            $createOrderResponse = Handler\AbstractHandler::factory("PaymentHandler", PayPalCommerce::loadModule(), SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->createOrder($paymentSource, $cartCalculatorModel, $invoice);
            \WHMCS\Session::set("remoteStorageToken", sprintf("oi-%s", $createOrderResponse->id));
            return new \WHMCS\Http\Message\JsonResponse(["id" => $createOrderResponse->id]);
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => "PayPal Create Order Error: " . $e->getMessage()]);
        }
    }
    public function invoiceOnApprove(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            $requestValues = $this->parseJson((string) $request->getBody());
            $this->assertExpectedProperties($requestValues, ["token", "invoiceid", "orderid"]);
            check_token("WHMCS.default", $requestValues->token);
            list($captureResult, $payMethod) = Handler\AbstractHandler::factory("PaymentHandler", PayPalCommerce::loadModule(), SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->invoiceOnApprove($requestValues->invoiceid, $requestValues->orderid);
            return new \WHMCS\Http\Message\JsonResponse($captureResult->get());
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => "PayPal On Approve Error: " . $e->getMessage()]);
        }
    }
    public function createSetupToken(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            $returnUrl = \App::getSystemURL() . "viewinvoice.php?id=0";
            $paymentSource = $this->paymentSourceFromOrderForm($returnUrl, $returnUrl . "&paymentfailed=true", \WHMCS\Config\Setting::getValue("CompanyName"), false)->setUsageType("MERCHANT")->setCustomerType("CONSUMER")->setPermitMultiplePaymentTokens(true);
            $createSetupTokenResponse = Handler\AbstractHandler::factory("PaymentHandler", PayPalCommerce::loadModule(), SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->createSetupToken($paymentSource);
            \WHMCS\Session::set("remoteStorageToken", sprintf("st-%s", $createSetupTokenResponse->getIdentifier()));
            return new \WHMCS\Http\Message\JsonResponse(["id" => $createSetupTokenResponse->getIdentifier()]);
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => "PayPal Create Setup Token Error: " . $e->getMessage()]);
        }
    }
    public function getSetupToken(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            $this->assertExpectedProperties((object) ["setuptoken" => $request->get("setuptoken")], ["setuptoken"]);
            $setupTokenResponse = Handler\AbstractHandler::factory("PaymentHandler", PayPalCommerce::loadModule(), SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->getSetupToken($request->get("setuptoken"));
            return new \WHMCS\Http\Message\JsonResponse(["success" => true, "email_address" => $setupTokenResponse->paymentSource()->paypal->email_address ?? ""]);
        } catch (\Exception $e) {
            \WHMCS\Session::set("payMethodCreateFailed", true);
            return new \WHMCS\Http\Message\JsonResponse(["success" => false, "error" => "PayPal Retrieve Setup Token Error: " . $e->getMessage()]);
        }
    }
    protected function paymentSourceFromOrderForm($returnUrl, string $cancelUrl, string $companyName, $vaultPayPal) : API\Entity\PaypalPaymentSource
    {
        $paymentSource = API\Entity\AbstractPaymentSource::factory("paypal");
        if($vaultPayPal) {
            $vaultCapable = ModuleConfiguration::fromPersistance()->getMerchantStatus(Environment::factory(ModuleConfiguration::fromPersistance()))->vaultCapable();
            $vaultCapable && $paymentSource->enableVaulting();
        }
        $paymentSource->setExperienceCancelUrl($cancelUrl)->setExperienceReturnUrl($returnUrl)->setExperienceBrandName($companyName);
        return $paymentSource;
    }
    protected function parseJson(string $jsonString)
    {
        $parsedJson = json_decode($jsonString);
        if(json_last_error() != JSON_ERROR_NONE) {
            throw new Exception\JsonInvalid("Unable to parse JSON: " . json_last_error_msg());
        }
        return $parsedJson;
    }
    protected function assertExpectedProperties($jsonObject, array $expectedProperties) : void
    {
        foreach ($expectedProperties as $expectedProperty) {
            if(!property_exists($jsonObject, $expectedProperty)) {
                throw new Exception\JsonInvalid("JSON response does not contain expected property: " . $expectedProperty);
            }
        }
    }
}

?>