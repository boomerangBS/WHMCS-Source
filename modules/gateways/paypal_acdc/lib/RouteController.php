<?php

namespace WHMCS\Module\Gateway\paypal_acdc;

class RouteController
{
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
            $paymentSource = $this->paymentSourceFromRequest($requestValues, $client, (bool) ($requestValues->vaultCard ?? false));
            $createOrderResponse = Handler\AbstractHandler::factory("payment_handler", Core::loadModule(), \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->createOrder($paymentSource, $cartCalculatorModel, $invoice);
            \WHMCS\Session::set("remoteStorageToken", sprintf("oi-%s", $createOrderResponse->id));
            return new \WHMCS\Http\Message\JsonResponse(["id" => $createOrderResponse->id]);
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => "PayPal Create Order Error: " . $e->getMessage()]);
        }
    }
    public function invoiceOnApprove(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            $this->assertJsonPostExpectedProperties($request, ["invoiceid", "orderid"]);
            $billingContactRequestValue = $request->get("billingcontact");
            $ccDescriptionRequestValue = $request->get("ccdescription");
            $billingContact = NULL;
            if($billingContactRequestValue == "new") {
                $billingContact = $this->newBillingContact(\Auth::client(), $request);
            } elseif(0 < $billingContactRequestValue) {
                $billingContact = \Auth::client()->contacts()->where("id", $billingContactRequestValue)->firstOrFail();
            }
            list($captureResult, $payMethod) = Handler\AbstractHandler::factory("payment_handler", Core::loadModule(), \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->invoiceOnApprove($request->get("invoiceid"), $request->get("orderid"));
            if(!is_null($payMethod)) {
                !is_null($ccDescriptionRequestValue) && $payMethod->setDescription($ccDescriptionRequestValue);
                if(!is_null($billingContact)) {
                    !$billingContact->exists && $billingContact->save();
                    $payMethod->contact()->associate($billingContact);
                }
                $payMethod->isDirty() or $payMethod->isDirty() && $payMethod->save();
            }
            return new \WHMCS\Http\Message\JsonResponse($captureResult->get());
        } catch (Exception\BillingDetailsInvalid $e) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => $e->getMessage()]);
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => "PayPal On Approve Error: " . $e->getMessage()]);
        }
    }
    public function createSetupToken(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            $client = \Auth::client();
            $payMethodsRoute = fqdnRoutePath("account-paymentmethods");
            $cardPaymentSource = $this->paymentSourceFromRequest((object) $request->request()->all(), $client, false, true)->setStoredCredentialByType(API\Entity\AbstractPaymentSource::CUSTOMER_FIRST)->setExperienceCancelUrl($payMethodsRoute)->setExperienceReturnUrl($payMethodsRoute);
            $createSetupTokenResponse = Handler\AbstractHandler::factory("payment_handler", Core::loadModule(), \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->createSetupToken($cardPaymentSource);
            if($request->get("vaultCard") != "false") {
                \WHMCS\Session::set("remoteStorageToken", sprintf("st-%s", $createSetupTokenResponse->getIdentifier()));
            }
            return new \WHMCS\Http\Message\JsonResponse(["id" => $createSetupTokenResponse->getIdentifier()]);
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["error" => "PayPal Create Setup Token Error: " . $e->getMessage()]);
        }
    }
    public function createPaymentToken(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        try {
            \WHMCS\Module\Gateway\paypal_ppcpv\Util::getAndDeleteSession("remoteStorageToken", "st-");
            $this->assertJsonPostExpectedProperties($request, ["setuptoken"]);
            $billingContact = NULL;
            if($request->get("billingcontact") == "new") {
                $billingContact = $this->newBillingContact(\Auth::client(), (object) $request->request()->all());
            } elseif(0 < $request->get("billingcontact")) {
                $billingContact = \Auth::client()->contacts()->where("id", $request->get("billingcontact"))->firstOrFail();
            }
            $createPaymentTokenResponse = Handler\AbstractHandler::factory("payment_handler", Core::loadModule(), \WHMCS\Module\Gateway\paypal_ppcpv\SystemConfiguration::singleton(\DI::make("app")), ModuleConfiguration::fromPersistance())->createPaymentToken((new API\Entity\SetupTokenPaymentSource())->setIdentifier($request->get("setuptoken")));
            $vaultTokenController = VaultTokenController::factoryModule(Core::loadModule());
            $paymentMethod = $vaultTokenController->saveVaultedToken(\Auth::client(), \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken::factory($createPaymentTokenResponse->getCustomerIdentifier(), $createPaymentTokenResponse->getVaultTokenIdentifier(), NULL, $createPaymentTokenResponse->getPaymentSource()), $billingContact, $request->get("description"));
            \WHMCS\Session::set("payMethodCreateSuccess", true);
            return new \WHMCS\Http\Message\JsonResponse(["success" => true]);
        } catch (\Exception $e) {
            \WHMCS\Session::set("payMethodCreateFailed", true);
            return new \WHMCS\Http\Message\JsonResponse(["success" => false, "error" => "PayPal Create Payment Token Error: " . $e->getMessage()]);
        }
    }
    protected function paymentSourceFromRequest($request, $client, $vaultCard = false, $useAlt3DS) : API\Entity\CardPaymentSource
    {
        $paymentSource = API\Entity\AbstractPaymentSource::factory("card");
        if(isset($request->custtype) && in_array($request->custtype, ["new", "add"]) || isset($request->billingcontact) && $request->billingcontact == "new") {
            $paymentSource->setName(trim($request->firstName . " " . $request->lastName));
            $paymentSource->setBillingAddress($request->address1, $request->address2, $request->city, $request->state, $request->postcode, $request->country);
        } elseif(!is_null($client) && isset($request->billingcontact) && 0 < $request->billingcontact) {
            $paymentSource->withContact($client->contacts()->where("id", $request->billingcontact)->firstOrFail());
        } elseif(!is_null($client)) {
            $paymentSource->withBillingContact($client);
        }
        if($vaultCard) {
            $vaultCapable = ModuleConfiguration::fromPersistance()->getMerchantStatus(\WHMCS\Module\Gateway\paypal_ppcpv\Environment::factory(ModuleConfiguration::fromPersistance()))->vaultCapable();
            $vaultCapable && $paymentSource->enableVaulting()->setStoredCredentialByType(API\Entity\AbstractPaymentSource::CUSTOMER_FIRST);
        }
        if($useAlt3DS) {
            return $paymentSource->enable3DSAlternate();
        }
        return $paymentSource->enable3DS();
    }
    private function newBillingContact(\WHMCS\User\Client $client, \WHMCS\Http\Message\ServerRequest $request)
    {
        if(!function_exists("checkDetailsareValid")) {
            require_once ROOTDIR . "/includes/clientfunctions.php";
        }
        $errors = checkDetailsareValid("", false, CHECKDETAILS_EMAIL_NONE, false, false);
        if(!empty($errors)) {
            throw new Exception\BillingDetailsInvalid($errors);
        }
        $billingContact = new \WHMCS\User\Client\Contact();
        $billingContact->clientId = $client->id;
        $billingContact->firstName = $request->get("firstname");
        $billingContact->lastName = $request->get("lastname");
        $billingContact->address1 = $request->get("address1");
        $billingContact->address2 = $request->get("address2");
        $billingContact->city = $request->get("city");
        $billingContact->state = $request->get("state");
        $billingContact->postcode = $request->get("postcode");
        $billingContact->country = $request->get("country");
        $billingContact->phoneNumber = $request->get("phonenumber");
        return $billingContact;
    }
    protected function parseJson(string $jsonString)
    {
        $parsedJson = json_decode($jsonString);
        if(json_last_error() != JSON_ERROR_NONE) {
            throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\JsonInvalid("Unable to parse JSON:" . json_last_error_msg());
        }
        return $parsedJson;
    }
    protected function assertExpectedProperties($jsonObject, array $expectedProperties) : void
    {
        foreach ($expectedProperties as $expectedProperty) {
            if(!property_exists($jsonObject, $expectedProperty)) {
                throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\JsonInvalid("JSON response does not contain expected property: " . $expectedProperty);
            }
        }
    }
    protected function assertJsonPostExpectedProperties(\WHMCS\Http\Message\ServerRequest $request, array $expectedProperties) : void
    {
        $requestValues = [];
        foreach ($expectedProperties as $expectedProperty) {
            $requestValues[$expectedProperty] = $request->get($expectedProperty);
        }
        $this->assertExpectedProperties((object) $requestValues, $expectedProperties);
    }
}

?>