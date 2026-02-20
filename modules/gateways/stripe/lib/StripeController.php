<?php


namespace WHMCS\Module\Gateway\Stripe;
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F7374726970652F6C69622F537472697065436F6E74726F6C6C65722E7068703078376664353934323435393733_
{
    protected $sessionKeyPrefix = "StripeIntentsData";
    protected $sessionKey;
    protected $data;
    public function load($invoiceId)
    {
        $sessionKey = $this->sessionKeyPrefix;
        if(!is_null($invoiceId) && 0 < strlen($invoiceId)) {
            $sessionKey = $this->sessionKeyPrefix . $invoiceId;
        }
        $rawData = WHMCS\Session::get($sessionKey);
        if($this->isValid($rawData)) {
            $this->data = $rawData;
            $this->sessionKey = $sessionKey;
            return true;
        }
        return false;
    }
    public function isValid($data)
    {
        return is_array($data);
    }
    public function data()
    {
        return $this->data;
    }
    public function delete()
    {
        $this->data = NULL;
        if(!is_null($this->sessionKey)) {
            return WHMCS\Session::delete($this->sessionKey);
        }
        return false;
    }
    public function clear() : int
    {
        $intentKeys = array_filter(WHMCS\Session::keys(), function ($key) {
            return strpos($key, $this->sessionKeyPrefix) === 0;
        });
        foreach ($intentKeys as $key) {
            WHMCS\Session::delete($key);
        }
        return count($intentKeys);
    }
}
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F7374726970652F6C69622F537472697065436F6E74726F6C6C65722E7068703078376664353934323461393665_
{
    public $method;
    public $stripeCustomer;
    public $invoiceId;
    public $clientId;
    private $request;
    private $client;
    private $invoice;
    private $customerType;
    private $paymentMethodId;
    private $billingContact;
    private $isSignup;
    public function processRequest(WHMCS\Http\Message\ServerRequest $request)
    {
        $this->request = $request;
        $this->customerType = $this->request->get("custtype");
        $this->invoiceId = $this->request->get("invoiceid");
        $this->paymentMethodId = $this->request->get("payment_method_id");
        $this->clientId = Auth::client()->id ?? NULL;
        $this->isSignup = empty($this->clientId);
        $this->loadRemotePaymentMethod()->loadInvoice()->selectAccount()->loadClient()->validateRequest()->loadStripeCustomer()->loadBillingContact()->updateRemotePayMethodAndSetBillingContact();
        return $this;
    }
    public function getClientId()
    {
        return $this->clientId;
    }
    public function getBillingContact()
    {
        return $this->billingContact;
    }
    private function isAddingAccount()
    {
        return $this->customerType === "add";
    }
    private function isLoggingIn()
    {
        return empty($this->clientId) && $this->customerType === "existing";
    }
    private function isSigningUp()
    {
        return (bool) $this->isSignup;
    }
    private function selectAccount() : self
    {
        if($this->isLoggingIn()) {
            $this->doLogin();
        } elseif($this->isAddingAccount()) {
            $this->clientId = NULL;
        } elseif($this->isSigningUp()) {
        }
        return $this;
    }
    private function validateRequest() : self
    {
        $errors = NULL;
        if($this->isAddingAccount()) {
            $this->validateAccountAdd(Auth::user(), $errors);
        } elseif($this->isSigningUp()) {
            $this->validateAccountAdd(NULL, $errors);
        }
        if(!empty($this->clientId)) {
            $this->validateNewBillingContact($errors);
        }
        $this->performCartValidation($errors);
        if(!empty($errors)) {
            throw new WHMCS\Exception\Information($errors);
        }
        return $this;
    }
    private function loadStripeCustomer() : self
    {
        if(empty($this->stripeCustomer) && !empty($this->method->customer)) {
            try {
                $this->stripeCustomer = Stripe\Customer::retrieve($this->method->customer);
            } catch (Exception $e) {
            }
        }
        if(empty($this->stripeCustomer) && !empty($this->client)) {
            $gatewayId = json_encode(WHMCS\Module\Gateway\Stripe\stripe_findFirstCustomerToken($this->client));
            if($gatewayId) {
                $jsonCheck = json_decode(WHMCS\Input\Sanitize::decode($gatewayId), true);
                if(is_array($jsonCheck) && array_key_exists("customer", $jsonCheck)) {
                    $this->stripeCustomer = Stripe\Customer::retrieve($jsonCheck["customer"]);
                } elseif(substr($gatewayId, 0, 3) == "cus") {
                    $this->stripeCustomer = Stripe\Customer::retrieve($gatewayId);
                }
            }
        }
        if(!$this->stripeCustomer) {
            $this->createRemoteCustomer();
        }
        return $this;
    }
    private function loadBillingContact() : self
    {
        if(!empty($this->client)) {
            $clientId = $this->client->id;
            if($this->client instanceof WHMCS\User\Client\Contact) {
                $clientId = $this->client->clientId;
            }
            if($this->client->billingContactId) {
                $billingContact = $this->client->billingContact;
            } else {
                $billingContact = $this->client;
            }
            if($this->request->get("billingcontact")) {
                $billingContactId = $this->request->get("billingcontact");
                if($billingContactId === "new") {
                    $billingContact = new WHMCS\User\Client\Contact();
                    $billingContact->clientId = $clientId;
                    $billingContact->firstName = $this->request->get("firstname");
                    $billingContact->lastName = $this->request->get("lastname");
                    $billingContact->email = $this->client->email;
                    $billingContact->address1 = $this->request->get("address1");
                    $billingContact->address2 = $this->request->get("address2");
                    $billingContact->city = $this->request->get("city");
                    $billingContact->state = $this->request->get("state");
                    $billingContact->postcode = $this->request->get("postcode");
                    $billingContact->country = $this->request->get("country");
                } else {
                    $billingContact = $this->client->contacts()->where("id", $billingContactId)->first();
                }
            }
            $this->billingContact = $billingContact;
        } elseif($this->isSigningUp() || $this->isAddingAccount()) {
            $billingContact = new WHMCS\User\Client\Contact();
            $billingContact->clientId = NULL;
            $billingContact->firstName = $this->request->get("firstname");
            $billingContact->lastName = $this->request->get("lastname");
            $billingContact->email = $this->request->get("email");
            $billingContact->address1 = $this->request->get("address1");
            $billingContact->address2 = $this->request->get("address2");
            $billingContact->city = $this->request->get("city");
            $billingContact->state = $this->request->get("state");
            $billingContact->postcode = $this->request->get("postcode");
            $billingContact->country = $this->request->get("country");
            $this->billingContact = $billingContact;
        }
        if(!$this->billingContact) {
            $this->loadBillingContactFromLocalPayMethod();
        }
        return $this;
    }
    private function loadRemotePaymentMethod() : self
    {
        if(empty($this->paymentMethodId)) {
            return $this;
        }
        try {
            $this->method = Stripe\PaymentMethod::retrieve($this->paymentMethodId);
        } catch (Exception $e) {
        }
        return $this;
    }
    private function loadInvoice() : self
    {
        if(!empty($this->stripeCustomer) || empty($this->invoiceId)) {
            return $this;
        }
        $invoice = WHMCS\Billing\Invoice::with("client")->find($this->invoiceId);
        if(!Auth::client() || Auth::client()->id != $invoice->clientId) {
            throw new InvalidArgumentException("Invalid Access Attempt");
        }
        $this->invoice = $invoice;
        $this->client = $invoice->client;
        $this->clientId = $invoice->client->id;
        return $this;
    }
    private function doLogin() : self
    {
        $loginEmail = $this->request->get("loginemail");
        $loginPw = WHMCS\Input\Sanitize::decode($this->request->get("loginpw")) ?: WHMCS\Input\Sanitize::decode($this->request->get("loginpassword"));
        $loginCheck = WHMCS\Module\Gateway\Stripe\localAPI("validatelogin", ["email" => $loginEmail, "password2" => $loginPw]);
        if($loginCheck["result"] === "success") {
            if($loginCheck["twoFactorEnabled"] === true) {
                throw new WHMCS\Exception\Authentication\RequiresSecondFactor();
            }
            $this->clientId = (int) $loginCheck["userid"];
            return $this;
        }
        throw new WHMCS\Exception\Information(Lang::trans("loginincorrect"));
    }
    private function validateAccountAdd($asUser = NULL, &$errors) : self
    {
        if(!function_exists("checkDetailsareValid")) {
            require_once ROOTDIR . "/includes/clientfunctions.php";
        }
        $signup = true;
        $checkClientsProfileUneditiableFields = true;
        $checkTermsOfService = false;
        $emailChecks = CHECKDETAILS_EMAIL_ALL;
        if(!is_null($asUser)) {
            if((new WHMCS\Validate())->validate("uniqueemail", "email", "", [$asUser->id, ""])) {
                $emailChecks ^= CHECKDETAILS_EMAIL_UNIQUE_USER ^ CHECKDETAILS_EMAIL_ASSOC_CLIENT;
            }
            $checkClientsProfileUneditiableFields = false;
            $checkTermsOfService = true;
            $signup = NULL;
        }
        $errors = WHMCS\Module\Gateway\Stripe\checkDetailsareValid("", $signup, $emailChecks, false, true, $checkClientsProfileUneditiableFields, false, false, $checkTermsOfService);
        return $this;
    }
    private function performCartValidation($errors) : self
    {
        if($this->request->get("custtype")) {
            if(!function_exists("cartValidationOnCheckout")) {
                require_once ROOTDIR . "/includes/cartfunctions.php";
            }
            $errors .= WHMCS\Module\Gateway\Stripe\cartValidationOnCheckout($this->clientId, true);
        }
        return $this;
    }
    private function loadClient() : self
    {
        if(!$this->client && !empty($this->clientId)) {
            $this->client = WHMCS\User\Client::find($this->clientId);
        }
        return $this;
    }
    private function validateNewBillingContact($errors) : self
    {
        if(!function_exists("checkDetailsareValid")) {
            require_once ROOTDIR . "/includes/clientfunctions.php";
        }
        if(App::isInRequest("billingcontact")) {
            $billingContactId = $this->request->get("billingcontact");
            if($billingContactId === "new") {
                $errors = WHMCS\Module\Gateway\Stripe\checkDetailsareValid($this->clientId, false, false, false, false);
            }
        }
        return $this;
    }
    private function loadBillingContactFromLocalPayMethod() : self
    {
        $localPayMethodId = $this->request->get("ccinfo");
        if(is_numeric($localPayMethodId)) {
            $payMethod = $this->client->payMethods()->where("id", $localPayMethodId)->first();
            if($payMethod) {
                $this->billingContact = $payMethod->contact;
            }
        }
        return $this;
    }
    private function createRemoteCustomer() : self
    {
        if($this->client) {
            $stripeCustomer = Stripe\Customer::create(WHMCS\Module\Gateway\Stripe\ApiPayload::customer($this->client, $this->client->id));
        } else {
            $name = trim(sprintf("%s %s", $this->request->get("firstname"), $this->request->get("lastname")));
            $email = $this->request->get("email");
            if(empty($name) || empty($email)) {
                throw new WHMCS\Exception\Information("Name and Email are required to pay with this gateway");
            }
            $stripeCustomer = Stripe\Customer::create(WHMCS\Module\Gateway\Stripe\ApiPayload::customer(App::self()));
            WHMCS\Session::set("StripeClientIdRequired", $stripeCustomer->id);
            unset($name);
            unset($email);
        }
        $this->stripeCustomer = $stripeCustomer;
        return $this;
    }
    public function updateRemotePayMethodAndSetBillingContact() : self
    {
        try {
            if(!empty($this->method) && substr($this->method->id, 0, 4) !== "card") {
                if($this->client) {
                    $billingContact = $this->billingContact ?: $this->client;
                    if(empty($billingContact->email)) {
                        $billingContact->email = $this->client->email;
                    }
                    $this->method = Stripe\PaymentMethod::update($this->method->id, ["billing_details" => ["email" => $billingContact->email, "name" => $billingContact->fullName, "address" => ["line1" => WHMCS\Module\Gateway\Stripe\ApiPayload::formatValue($billingContact->address1), "line2" => WHMCS\Module\Gateway\Stripe\ApiPayload::formatValue($billingContact->address2), "city" => WHMCS\Module\Gateway\Stripe\ApiPayload::formatValue($billingContact->city), "state" => WHMCS\Module\Gateway\Stripe\ApiPayload::formatValue($billingContact->state), "country" => WHMCS\Module\Gateway\Stripe\ApiPayload::formatValue($billingContact->country), "postal_code" => WHMCS\Module\Gateway\Stripe\ApiPayload::formatValue($billingContact->postcode)]], "metadata" => ["id" => $this->clientId, "fullName" => $this->client->fullName, "email" => $this->client->email]]);
                } else {
                    $this->method = Stripe\PaymentMethod::update($this->method->id, WHMCS\Module\Gateway\Stripe\ApiPayload::paymentContact(App::self()));
                }
            }
        } catch (Stripe\Exception\CardException $e) {
            $e->getStripeCode();
            switch ($e->getStripeCode()) {
                case "incorrect_zip":
                    throw new WHMCS\Exception\Information($e->getMessage());
                    break;
                case "card_declined":
                    throw new WHMCS\Exception\Gateways\Declined(Lang::trans("genericPaymentDeclined"));
                    break;
                default:
                    throw $e;
            }
        }
        return $this;
    }
}
class StripeController
{
    public function intent(\WHMCS\Http\Message\ServerRequest $request)
    {
        $token = $request->get("token");
        check_token("WHMCS.default", $token);
        $gateway = new \WHMCS\Module\Gateway();
        if(!$gateway->load("stripe")) {
            return new \WHMCS\Http\Message\JsonResponse(["validation_feedback" => "Module Not Active"]);
        }
        $gatewayParams = $gateway->getParams();
        stripe_start_stripe($gatewayParams);
        try {
            $requestHandler = $this->getIntentRequestHandler();
            $requestHandler->processRequest($request);
        } catch (\WHMCS\Exception\Authentication\RequiresSecondFactor $e) {
            return new \WHMCS\Http\Message\JsonResponse(["two_factor" => true]);
        } catch (\WHMCS\Exception\Information $e) {
            return new \WHMCS\Http\Message\JsonResponse(["validation_feedback" => $e->getMessage(), "reloadCaptcha" => (bool) (!\WHMCS\Session::get("CartValidationOnCheckout"))]);
        } catch (\Throwable $t) {
            return new \WHMCS\Http\Message\JsonResponse(["validation_feedback" => \Lang::trans("remoteTransError"), "reloadCaptcha" => !\WHMCS\Session::get("CartValidationOnCheckout")]);
        }
        try {
            $cartData = [];
            if(!\Auth::user()) {
                if(!function_exists("calcCartTotals")) {
                    require ROOTDIR . "/includes/orderfunctions.php";
                }
                if(!$requestHandler->getClientId()) {
                    $_SESSION["cart"]["user"]["state"] = $request->get("state");
                    $_SESSION["cart"]["user"]["country"] = $request->get("country");
                }
                if(!$requestHandler->getClientId() && \WHMCS\Billing\Tax\Vat::isTaxEnabled()) {
                    $taxId = $request->get("tax_id");
                    if(!$taxId && \WHMCS\Billing\Tax\Vat::getFieldName() !== "tax_id") {
                        $customFieldId = (int) \WHMCS\Config\Setting::getValue("TaxVatCustomFieldId");
                        $taxId = $request->get("customfield")[$customFieldId];
                    }
                    if(\WHMCS\Config\Setting::getValue("TaxEUTaxExempt") && !empty($taxId)) {
                        $validNumber = \WHMCS\Billing\Tax\Vat::validateNumber($taxId);
                        if($validNumber && in_array($request->get("country"), array_keys(\WHMCS\Billing\Tax\Vat::EU_COUNTRIES))) {
                            $_SESSION["cart"]["user"]["taxexempt"] = true;
                            if(\WHMCS\Config\Setting::getValue("TaxEUHomeCountryNoExempt") && $request->get("country") == \WHMCS\Config\Setting::getValue("TaxEUHomeCountry")) {
                                $_SESSION["cart"]["user"]["taxexempt"] = false;
                            }
                        }
                    }
                }
                $cartData = calcCartTotals(\Auth::client(), false, false);
            }
            $intentResolver = $this->getPaymentIntentHandler();
            if(!$intentResolver->load($requestHandler->invoiceId)) {
                throw new \InvalidArgumentException("Invalid or Missing Payment Information - Please Reload and Try Again");
            }
            $intentsData = $intentResolver->data();
            if(array_key_exists("rawtotal", $cartData)) {
                $currencyData = \Currency::factoryForClientArea();
                $amount = $cartData["rawtotal"];
                $currencyCode = $currencyData["code"];
                if(isset($gatewayParams["convertto"]) && $gatewayParams["convertto"]) {
                    $currencyCode = \WHMCS\Database\Capsule::table("tblcurrencies")->where("id", "=", (int) $gatewayParams["convertto"])->value("code");
                    $amount = convertCurrency($amount, $currencyData["id"], $gatewayParams["convertto"]);
                }
                $amount = ApiPayload::formatAmountOutbound($amount, $currencyCode);
                $intentsData["amount"] = $amount;
                $intentsData["currency"] = strtolower($currencyCode);
            }
            if(!empty($requestHandler->method)) {
                $intentsData["payment_method"] = $requestHandler->method->id;
                $intentsData["confirm"] = true;
                $intentsData["save_payment_method"] = true;
            }
            $intentsData["confirmation_method"] = "automatic";
            $intentsData["capture_method"] = "manual";
            $intentsData["customer"] = $requestHandler->stripeCustomer->id;
            $intentsData["setup_future_usage"] = "off_session";
            $intent = \Stripe\PaymentIntent::create($intentsData);
        } catch (\Exception $e) {
            if($requestHandler->invoiceId) {
                $user = "";
                if(defined("CLIENTAREA")) {
                    $user = "Client";
                } elseif(defined("ADMINAREA")) {
                    $user = "Admin";
                }
                $history = new \WHMCS\Billing\Payment\Transaction\History();
                $history->gateway = "Stripe";
                $history->invoiceId = $requestHandler->invoiceId;
                $history->transactionId = "N/A";
                $history->remoteStatus = "Declined";
                $history->description = "Initiated by " . $user . ". Error: " . $e->getMessage();
                $history->save();
            }
            return new \WHMCS\Http\Message\JsonResponse(["validation_feedback" => $e->getMessage()]);
        }
        unset($intentResolver);
        $storeIntent = function () use($intent) {
            \WHMCS\Session::set("remoteStorageToken", $intent->id);
        };
        switch ($intent->status) {
            case "requires_payment_method":
                $storeIntent();
                $paymentContact = ApiPayload::paymentContact($requestHandler->getBillingContact());
                $response = ["requires_payment" => true, "success" => false, "token" => $intent->client_secret];
                $response = array_merge($response, $paymentContact);
                break;
            case "requires_source_action":
            case "requires_action":
                $storeIntent();
                $response = ["requires_action" => true, "success" => false, "token" => $intent->client_secret];
                break;
            case "requires_capture":
            case "succeeded":
                $storeIntent();
                $response = ["success" => true, "requires_action" => false];
                break;
            default:
                $response = ["validation_feedback" => "Invalid PaymentIntent status"];
                return new \WHMCS\Http\Message\JsonResponse($response);
        }
    }
    public function setupIntent(\WHMCS\Http\Message\ServerRequest $request)
    {
        $token = $request->get("token");
        check_token("WHMCS.default", $token);
        $gateway = new \WHMCS\Module\Gateway();
        if(!$gateway->load("stripe")) {
            return new \WHMCS\Http\Message\JsonResponse(["validation_feedback" => "Module Not Active"]);
        }
        stripe_start_stripe($gateway->getParams());
        $setupIntent = \Stripe\SetupIntent::create();
        \WHMCS\Session::set("remoteStorageToken", $setupIntent->id);
        return new \WHMCS\Http\Message\JsonResponse(["success" => true, "setup_intent" => $setupIntent->client_secret]);
    }
    public function add(\WHMCS\Http\Message\ServerRequest $request)
    {
        $token = $request->get("token");
        check_token("WHMCS.default", $token);
        return $this->addProcess($request, true);
    }
    public function adminAdd(\WHMCS\Http\Message\ServerRequest $request)
    {
        return $this->addProcess($request);
    }
    protected function addProcess(\WHMCS\Http\Message\ServerRequest $request, $sessionUserId = false)
    {
        $paymentMethodId = $request->get("payment_method_id");
        $userId = (int) $request->get("user_id");
        if($sessionUserId) {
            $userId = \Auth::client()->id;
        }
        if(!$userId) {
            $error = "User Id not found in request params";
            if($sessionUserId) {
                $error = "Login session not found";
            }
            return new \WHMCS\Http\Message\JsonResponse(["validation_feedback" => $error]);
        }
        $gateway = new \WHMCS\Module\Gateway();
        if(!$gateway->load("stripe")) {
            return new \WHMCS\Http\Message\JsonResponse(["validation_feedback" => "Module Not Active"]);
        }
        stripe_start_stripe($gateway->getParams());
        try {
            $client = \WHMCS\User\Client::findOrFail($userId);
            $existingMethod = stripe_findFirstCustomerToken($client);
            $stripeCustomer = NULL;
            $gatewayId = $client->paymentGatewayToken;
            $billingContactId = \App::getFromRequest("billingcontact");
            $billingContact = NULL;
            if($billingContactId) {
                $billingContact = $client->contacts()->where("id", $billingContactId)->first();
            }
            if(!$billingContact) {
                $billingContact = $client;
            }
            if($gatewayId) {
                $jsonCheck = json_decode(\WHMCS\Input\Sanitize::decode($gatewayId), true);
                if(is_array($jsonCheck) && array_key_exists("customer", $jsonCheck)) {
                    $stripeCustomer = \Stripe\Customer::retrieve($jsonCheck["customer"]);
                } elseif(substr($gatewayId, 0, 3) == "cus") {
                    $stripeCustomer = \Stripe\Customer::retrieve($gatewayId);
                }
            }
            if(!$stripeCustomer && $existingMethod && is_array($existingMethod) && array_key_exists("customer", $existingMethod)) {
                $stripeCustomer = \Stripe\Customer::retrieve($existingMethod["customer"]);
            }
            if(!$stripeCustomer) {
                $stripeCustomer = \Stripe\Customer::create(ApiPayload::customer($client, $client->id));
            }
            $method = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            if(!$method->customer) {
                $method->attach(["customer" => $stripeCustomer->id]);
            }
            $billingContactEmail = $billingContact->email;
            if(!$billingContactEmail) {
                $billingContactEmail = $client->email;
            }
            $method = \Stripe\PaymentMethod::update($method->id, ["billing_details" => ["email" => $billingContactEmail, "name" => $billingContact->fullName, "address" => ["line1" => ApiPayload::formatValue($billingContact->address1), "line2" => ApiPayload::formatValue($billingContact->address2), "city" => ApiPayload::formatValue($billingContact->city), "state" => ApiPayload::formatValue($billingContact->state), "country" => ApiPayload::formatValue($billingContact->country), "postal_code" => ApiPayload::formatValue($billingContact->postcode)]], "metadata" => ["id" => $userId, "fullName" => $client->fullName, "email" => $client->email]]);
            $response = ["success" => true, "requires_action" => false, "token" => $method->id];
        } catch (\Exception $e) {
            $response = ["validation_feedback" => $e->getMessage()];
        }
        return new \WHMCS\Http\Message\JsonResponse($response);
    }
    protected function getPaymentIntentHandler()
    {
        return new func_num_args();
    }
    public function getIntentRequestHandler()
    {
        return new func_num_args();
    }
}

?>