<?php

if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
function stripe_sepa_MetaData()
{
    return ["APIVersion" => 0, "gatewayType" => WHMCS\Module\Gateway::GATEWAY_BANK, "failedEmail" => "Direct Debit Payment Failed", "successEmail" => "Direct Debit Payment Confirmation", "pendingEmail" => "Direct Debit Payment Pending", "noCurrencyConversion" => true, "supportedCurrencies" => ["EUR"]];
}
function stripe_sepa_config()
{
    $invalidDescriptorChars = implode(", ", stripe_sepa_statement_descriptor_invalid_characters());
    $rakURL = WHMCS\Module\Gateway\Stripe\Constant::RAK_APP_URL;
    $currentConfig = WHMCS\Module\GatewaySetting::getForGateway("stripe_ach");
    $currentSecretKey = $currentConfig["secretKey"] ?? "";
    $config = ["FriendlyName" => ["Type" => "System", "Value" => "Stripe SEPA"]];
    if(!$currentSecretKey || substr($currentSecretKey, 0, 2) === "sk") {
        $config["rakApp"] = ["FriendlyName" => "API Key Generation", "Description" => "<div class=\"alert alert-info top-margin-5 bottom-margin-5\">\n    <a href=\"" . $rakURL . "\" class=\"autoLinked\">\n        Click here\n    </a> to activate and configure the WHMCS Restricted Auth Keys app in your Stripe account.<br>\n    Enter the generated keys in the appropriate section and click Save Changes.\n</div>"];
    }
    $config = array_merge($config, ["publishableKey" => ["FriendlyName" => "Stripe Publishable API Key", "Type" => "text", "Size" => "30", "Description" => "Your publishable API key identifies your website to Stripe during communications. This can be obtained from <a href=\"https://dashboard.stripe.com/account/apikeys\" class=\"autoLinked\">here</a>"], "secretKey" => ["FriendlyName" => "Stripe Secret API Key", "Type" => "text", "Size" => "30", "Description" => "Your secret API Key ensures only communications from Stripe are validated."], "webhookEndpointSecret" => ["FriendlyName" => "Stripe SEPA WebHook Endpoint Secret", "Type" => "password", "Size" => "30", "Description" => "Automatically generated web-hook secret key."], "statementDescriptor" => ["FriendlyName" => "Statement Descriptor", "Type" => "text", "Size" => 25, "Default" => "{CompanyName}", "Description" => "Available merge field tags: <strong>{CompanyName} {InvoiceNumber}</strong>\n<div class=\"alert alert-info top-margin-5 bottom-margin-5\">\n    Displayed on your customer's credit card statement.<br />\n    <strong>Maximum length of 22 characters</strong>, and must not contain any of the following:\n    <span style=\"font-family: monospace\">" . $invalidDescriptorChars . "</span><br />\n    This will be appended to the statement descriptor defined in the Stripe Account.\n</div>"]]);
    try {
        WHMCS\Module\Gateway::factory("stripe");
        $config["copyStripeConfig"] = ["FriendlyName" => "Use Stripe Configuration", "Type" => "yesno", "Description" => "Use the configuration from Stripe to configure the Publishable Key, Private Key and Statement Descriptor"];
    } catch (Exception $e) {
    }
    $currencies = WHMCS\Billing\Currency::where("code", "!=", "EUR")->pluck("code");
    $usageNotes = [];
    if(count($currencies)) {
        $usageNotes[] = "<strong>Unsupported Currencies.</strong> You have one or more currencies configured that are not supported by Stripe SEPA. Invoices using currencies SEPA does not support will be unable to be paid using SEPA. <a href=\"https://go.whmcs.com/2149/stripe-sepa\" target=\"_blank\">Learn more</a>";
    }
    if($usageNotes) {
        $config["UsageNotes"] = ["Type" => "System", "Value" => implode("<br>", $usageNotes)];
    }
    return $config;
}
function stripe_sepa_nolocalcc()
{
}
function stripe_sepa_config_validate(array $params = [])
{
    if(!empty($params["copyStripeConfig"])) {
        return NULL;
    }
    if(isset($params["statementDescriptor"]) && 0 < strlen($params["statementDescriptor"])) {
        $descriptorCheck = str_replace(stripe_sepa_statement_descriptor_invalid_characters(), "", $params["statementDescriptor"]);
        if(strlen($params["statementDescriptor"]) != strlen($descriptorCheck)) {
            throw new WHMCS\Exception\Module\InvalidConfiguration("Invalid characters present in Statement Descriptor Suffix");
        }
        unset($descriptorCheck);
    }
    try {
        if($params["publishableKey"] && substr($params["publishableKey"], 0, 3) === "pk_" && $params["secretKey"]) {
            stripe_sepa_start_stripe($params);
            Stripe\Customer::all();
            Stripe\Stripe::setApiKey($params["publishableKey"]);
            Stripe\Customer::all();
        } else {
            throw new WHMCS\Exception\Module\InvalidConfiguration("Please ensure your Stripe API keys are correct and try again.");
        }
    } catch (Exception $e) {
        $checkMessage = substr($e->getMessage(), 0, 55);
        if($checkMessage != "This API call cannot be made with a publishable API key") {
            throw new WHMCS\Exception\Module\InvalidConfiguration($e->getMessage());
        }
    }
}
function stripe_sepa_config_post_save(array $params = [])
{
    if(array_key_exists("copyStripeConfig", $params) && $params["copyStripeConfig"]) {
        try {
            $gatewayInterface = WHMCS\Module\Gateway::factory("stripe");
            $gatewayParams = $gatewayInterface->getParams();
            $copiedParams = ["publishableKey" => $gatewayParams["publishableKey"], "secretKey" => $gatewayParams["secretKey"], "statementDescriptor" => $gatewayParams["statementDescriptor"], "copyStripeConfig" => ""];
            foreach ($copiedParams as $copiedParam => $value) {
                WHMCS\Module\GatewaySetting::setValue("stripe_sepa", $copiedParam, $value);
            }
            $params = array_merge($params, $copiedParams);
        } catch (Exception $e) {
        }
    }
    if(array_key_exists("secretKey", $params) && $params["secretKey"]) {
        $requiredEvents = ["charge.failed", "charge.succeeded"];
        $notificationUrl = App::getSystemURL() . "modules/gateways/callback/stripe_sepa.php";
        stripe_sepa_start_stripe($params);
        $webHooks = Stripe\WebhookEndpoint::all([]);
        foreach ($webHooks->data as $webHook) {
            if($webHook->url == $notificationUrl && $webHook->status == "enabled") {
                if(empty($params["webhookEndpointSecret"])) {
                    $webHook->delete();
                } else {
                    foreach ($requiredEvents as $requiredEvent) {
                        if(!in_array($requiredEvent, $webHook->enabled_events)) {
                            $webHook->delete();
                        }
                    }
                    return NULL;
                }
            }
        }
        $webHook = Stripe\WebhookEndpoint::create(["url" => $notificationUrl, "enabled_events" => $requiredEvents]);
        WHMCS\Module\GatewaySetting::setValue("stripe_sepa", "webhookEndpointSecret", $webHook->secret);
    }
}
function stripe_sepa_deactivate(array $params)
{
    $notificationUrl = App::getSystemURL() . "modules/gateways/callback/stripe_sepa.php";
    stripe_sepa_start_stripe($params);
    $webHooks = Stripe\WebhookEndpoint::all([]);
    foreach ($webHooks->data as $webHook) {
        if($webHook->url == $notificationUrl && $webHook->status == "enabled") {
            $webHook->delete();
        }
    }
}
function stripe_sepa_storeremote(array $params)
{
    stripe_sepa_start_stripe($params);
    switch ($params["action"]) {
        case "create":
            $remoteToken = $params["remoteStorageToken"];
            if(substr($remoteToken, 0, 4) !== "seti") {
                return ["status" => "error", "rawdata" => ["message" => "Invalid Remote Token", "token" => $remoteToken]];
            }
            try {
                $paymentIntent = Stripe\SetupIntent::retrieve($remoteToken);
                $paymentMethod = Stripe\PaymentMethod::retrieve($paymentIntent->payment_method);
                $accountNumber = $paymentMethod->sepa_debit->last4;
                $bankCode = $paymentMethod->sepa_debit->bank_code;
                $remoteTokenData = ["customer" => $paymentIntent->customer, "account" => $paymentMethod->id];
                return ["status" => "success", "rawdata" => $paymentIntent->jsonSerialize(), "remoteToken" => json_encode($remoteTokenData), "accountNumber" => $accountNumber, "routingNumber" => $bankCode];
            } catch (Exception $e) {
                return ["status" => "error", "rawdata" => $e->getMessage()];
            }
            break;
        case "delete":
            try {
                $remoteToken = stripe_sepa_parseGatewayToken($params["gatewayid"]);
                if(!$remoteToken) {
                    return ["status" => "error", "rawdata" => ["error" => "Invalid Remote Token for Gateway", "data" => $params["gatewayid"]]];
                }
                if(substr($remoteToken["account"], 0, 2) === "ba") {
                    Stripe\Customer::deleteSource($remoteToken["customer"], $remoteToken["account"]);
                } else {
                    Stripe\PaymentMethod::retrieve($remoteToken["account"])->detach();
                }
                return ["status" => "success"];
            } catch (Exception $e) {
                return ["status" => "error", "rawdata" => $e->getMessage()];
            }
            break;
        case "update":
            return ["gatewayid" => $params["remoteStorageToken"], "rawdata" => "Pay Method Description has been updated", "status" => "success"];
            break;
        default:
            return ["status" => "error", "rawdata" => "Invalid Action Request"];
    }
}
function stripe_sepa_capture(array $params)
{
    try {
        stripe_sepa_start_stripe($params);
        $remoteToken = stripe_sepa_parseGatewayToken($params["gatewayid"]);
        if(!$remoteToken) {
            throw new InvalidArgumentException("Invalid Remote Token For Gateway: " . $params["gatewayid"]);
        }
        if($params["currency"] != "EUR") {
            throw new InvalidArgumentException("Invalid Currency For Gateway: " . $params["currency"]);
        }
        $paymentAmount = WHMCS\Module\Gateway\Stripe\ApiPayload::formatAmountOutbound($params["amount"], $params["currency"]);
        $searchCharge = Stripe\Charge::search(["query" => "customer:'" . $remoteToken["customer"] . "' AND metadata['id']:'" . $params["invoiceid"] . "'"]);
        foreach ($searchCharge as $charge) {
            $chargeAmount = $charge->amount;
            $chargeStatus = $charge->status;
            if($chargeStatus === "failed") {
            } elseif((string) $chargeAmount === $paymentAmount) {
                $transaction = [];
                try {
                    $transaction = Stripe\BalanceTransaction::retrieve($charge->balance_transaction);
                    $transaction = $transaction->jsonSerialize();
                } catch (Throwable $t) {
                }
                return ["status" => "pending", "rawdata" => ["charge" => $charge->jsonSerialize(), "transaction" => $transaction]];
            }
        }
        $intentArray = ["payment_method_types" => ["sepa_debit"], "payment_method" => $remoteToken["account"], "customer" => $remoteToken["customer"], "confirm" => true, "amount" => $paymentAmount, "currency" => "eur", "metadata" => ["customer" => $params["clientdetails"]["id"], "id" => $params["invoiceid"], "invoicenum" => $params["invoicenum"]], "statement_descriptor" => stripe_sepa_statement_descriptor($params)];
        $paymentIntent = Stripe\PaymentIntent::create($intentArray);
        $latestCharge = $paymentIntent->latest_charge;
        $charge = Stripe\Charge::retrieve($latestCharge);
        try {
            $transaction = Stripe\BalanceTransaction::retrieve($charge->balance_transaction);
            $transaction = $transaction->jsonSerialize();
        } catch (Throwable $t) {
            $transaction = ["t-message" => "Currently unable to retrieve transaction", "t-detail" => $t->getMessage(), "t-reference" => $charge->balance_transaction];
        }
        return ["status" => "pending", "rawdata" => ["charge" => $charge->jsonSerialize(), "transaction" => $transaction]];
    } catch (Exception $e) {
        return ["status" => "error", "rawdata" => ["gatewayId" => $params["gatewayid"], "currency" => $params["currency"], "message" => $e->getMessage()], "declinereason" => $e->getMessage()];
    }
}
function stripe_sepa_bank_account_input(array $params)
{
    stripe_sepa_start_stripe($params);
    $existingSubmittedToken = "";
    $assetHelper = DI::make("asset");
    $now = time();
    $token = App::getFromRequest("remoteStorageToken");
    if($token && substr($token, 0, 4) != "seti") {
        $token = "";
    }
    $customerId = NULL;
    if(!$token && $params["gatewayid"]) {
        $remoteToken = stripe_sepa_parseGatewayToken($params["gatewayid"]);
        if($remoteToken && array_key_exists("account", $remoteToken)) {
            $existingSubmittedToken = $remoteToken["account"];
        }
        if($remoteToken && !empty($remoteToken["customer"])) {
            $customerId = $remoteToken["customer"];
        }
    }
    if($token) {
        $existingSubmittedToken = $token;
    }
    if(is_null($customerId)) {
        $customerId = stripe_sepa_create_customer($params);
    }
    $jsOutput = "existingToken = '" . $existingSubmittedToken . "';";
    $companyName = WHMCS\Config\Setting::getValue("CompanyName");
    $sepaJs = $assetHelper->getWebRoot() . "/modules/gateways/stripe_sepa/stripe_sepa.min.js?a=" . $now;
    $description = stripe_sepa_statement_descriptor($params);
    $intentsArray = ["description" => $description, "customer" => $customerId, "payment_method_types" => ["sepa_debit"], "usage" => "off_session", "metadata" => ["customer" => $params["clientdetails"]["id"], "id" => $params["invoiceid"], "invoicenum" => $params["invoicenum"]]];
    $intent = Stripe\SetupIntent::create($intentsArray);
    $lang = ["iban" => Lang::trans("paymentMethods.iban"), "mandate_acceptance" => addslashes(Lang::trans("paymentMethods.mandateAcceptance", [":companyName" => WHMCS\Config\Setting::getValue("CompanyName")])), "acctHolderError" => addslashes(Lang::trans("validation.filled", [":attribute" => Lang::trans("paymentMethodsManage.accountHolderName")])), "addressError" => addslashes(Lang::trans("validation.filled", [":attribute" => Lang::trans("clientareaaddress1")])), "countryError" => addslashes(Lang::trans("validation.filled", [":attribute" => Lang::trans("clientareacountry")])), "requires_payment_method" => "Confirmation failed. Attempt again with a different payment method."];
    $apiVersion = WHMCS\Module\Gateway\Stripe\Constant::$apiVersion;
    return "<script type=\"text/javascript\" src=\"" . $sepaJs . "\"></script>\n<script type=\"text/javascript\">\n\nvar existingToken = null,\n    clientToken = '" . $intent->client_secret . "',\n    companyName = '" . $companyName . "',\n    clientEmail = '" . $params["clientdetails"]["email"] . "',\n    stripe = null,\n    elements = null,\n    iban = null,\n    lang = null;\n\n\$(document).ready(function() {\n    stripe = Stripe('" . $params["publishableKey"] . "');\n    stripe.api_version = \"" . $apiVersion . "\";\n    elements = stripe.elements();\n    iban = elements.create('iban', {\n        supportedCountries: ['SEPA'],\n    });\n    lang = {\n        iban: '" . $lang["iban"] . "',\n        mandate_acceptance: '" . $lang["mandate_acceptance"] . "',\n        acctHolderError: '" . $lang["acctHolderError"] . "',\n        addressError: '" . $lang["addressError"] . "',\n        countryError: '" . $lang["countryError"] . "',\n        requires_payment_method: '" . $lang["requires_payment_method"] . "'\n    };\n    " . $jsOutput . "\n    initStripeSEPA();\n});    \n</script>";
}
function stripe_sepa_refund(array $params = [])
{
    $amount = stripe_sepa_formatAmount($params["amount"], $params["currency"]);
    stripe_sepa_start_stripe($params);
    $client = WHMCS\User\Client::find($params["clientdetails"]["userid"]);
    try {
        $transaction = Stripe\BalanceTransaction::retrieve($params["transid"]);
        $refund = Stripe\Refund::create(["charge" => $transaction->source, "amount" => $amount]);
        $refundTransaction = Stripe\BalanceTransaction::retrieve($refund->balance_transaction);
        $transactionFeeCurrency = WHMCS\Database\Capsule::table("tblcurrencies")->where("code", "=", strtoupper($refundTransaction->fee_details[0]->currency))->first(["id"]);
        $refundTransactionFee = 0;
        if($transactionFeeCurrency) {
            $refundTransactionFee = convertCurrency($refundTransaction->fee / -100, $transactionFeeCurrency->id, $params["convertto"] ?: $client->currencyId);
        }
        return ["transid" => $refundTransaction->id, "rawdata" => array_merge($refund->jsonSerialize(), $refundTransaction->jsonSerialize()), "status" => "success", "fees" => $refundTransactionFee];
    } catch (Exception $e) {
        return ["status" => "error", "rawdata" => $e->getMessage()];
    }
}
function stripe_sepa_formatValue($value)
{
    return $value !== "" ? $value : NULL;
}
function stripe_sepa_formatAmount($amount, $currencyCode)
{
    $currenciesWithoutDecimals = WHMCS\Module\Gateway\Stripe\Constant::STRIPE_CURRENCIES_NO_DECIMALS;
    $currencyCode = strtoupper($currencyCode);
    $isNoDecimalCurrency = in_array($currencyCode, $currenciesWithoutDecimals);
    $amount = str_replace([",", "."], "", $amount);
    if($isNoDecimalCurrency) {
        $amount = round($amount / 100);
    }
    return $amount;
}
function stripe_sepa_start_stripe(array $params)
{
    Stripe\Stripe::setAppInfo(WHMCS\Module\Gateway\Stripe\Constant::$appName, App::getVersion()->getMajor(), WHMCS\Module\Gateway\Stripe\Constant::$appUrl, WHMCS\Module\Gateway\Stripe\Constant::$appPartnerId);
    Stripe\Stripe::setApiKey($params["secretKey"]);
    Stripe\Stripe::setApiVersion(WHMCS\Module\Gateway\Stripe\Constant::$apiVersion);
}
function stripe_sepa_parseGatewayToken($data)
{
    $data = json_decode($data, true);
    if($data && is_array($data)) {
        return $data;
    }
    return [];
}
function stripe_sepa_findFirstCustomerToken(WHMCS\User\Contracts\ContactInterface $client)
{
    $clientToUse = $client;
    if($clientToUse instanceof WHMCS\User\Client\Contact) {
        $clientToUse = $clientToUse->client;
    }
    foreach ($clientToUse->payMethods as $payMethod) {
        if($payMethod->gateway_name == "stripe_sepa") {
            $payment = $payMethod->payment;
            $token = stripe_sepa_parsegatewaytoken($payment->getRemoteToken());
            if($token) {
                return $token;
            }
        }
    }
    return NULL;
}
function stripe_sepa_findFirstStripeCustomerId(WHMCS\User\Contracts\ContactInterface $client)
{
    $clientToUse = $client;
    if($clientToUse instanceof WHMCS\User\Client\Contact) {
        $clientToUse = $clientToUse->client;
    }
    foreach ($clientToUse->payMethods as $payMethod) {
        if(in_array($payMethod->gateway_name, ["stripe", "stripe_ach", "stripe_sepa"])) {
            $payment = $payMethod->payment;
            $token = stripe_sepa_parsegatewaytoken($payment->getRemoteToken());
            if($token) {
                return $token["customer"];
            }
        }
    }
    $remoteCustomers = Stripe\Customer::all(["email" => $clientToUse->email, "limit" => 15]);
    foreach ($remoteCustomers->data as $customer) {
        $metaId = !empty($customer->metadata->clientId) ? (int) $customer->metadata->clientId : 0;
        if($metaId === $clientToUse->id) {
            return $customer->id;
        }
    }
    return NULL;
}
function stripe_sepa_statement_descriptor(array $params)
{
    $defaultDescriptor = Lang::trans("carttitle");
    $descriptor = $defaultDescriptor;
    if(isset($params["statementDescriptor"]) && 0 < strlen($params["statementDescriptor"])) {
        $descriptor = $params["statementDescriptor"];
        $invoiceNumber = array_key_exists("invoicenum", $params) && $params["invoicenum"] ? $params["invoicenum"] : $params["invoiceid"];
        $descriptor = str_replace(["{CompanyName}", "{InvoiceNumber}"], [WHMCS\Config\Setting::getValue("CompanyName"), $invoiceNumber], $descriptor);
    }
    $descriptor = voku\helper\ASCII::to_transliterate($descriptor);
    $descriptor = trim(str_replace(stripe_sepa_statement_descriptor_invalid_characters(), "", $descriptor));
    if(strlen($descriptor) == 0) {
        $descriptor = $defaultDescriptor;
    }
    $descriptor = substr($descriptor, -22);
    return $descriptor;
}
function stripe_sepa_statement_descriptor_invalid_characters()
{
    return [">", "<", "'", "\"", "*"];
}
function stripe_sepa_create_customer(array $params)
{
    $client = $params["clientdetails"]["model"];
    if($client instanceof WHMCS\User\Client\Contact) {
        $client = $client->client;
    }
    $stripeCustomer = Stripe\Customer::create(["description" => "Customer for " . $client->fullName . " (" . $client->email . ")", "email" => $client->email, "metadata" => ["id" => $client->id, "fullName" => $client->fullName, "email" => $client->email]]);
    return $stripeCustomer->id;
}

?>