<?php

namespace WHMCS\Admin\Billing;

class BillingController
{
    public function newInvoice(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        return new \WHMCS\Http\Message\JsonResponse(["body" => view("admin.billing.invoice.new", ["gateways" => \WHMCS\Module\GatewaySetting::getActiveGatewayFriendlyNames(), "invoiceGenerationDays" => \WHMCS\Config\Setting::getValue("CreateInvoiceDaysBefore")])]);
    }
    public function createInvoice(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        $clientId = $request->get("client");
        $gateway = $request->get("gateway");
        $invoiceDate = $request->get("date");
        $dueDate = $request->get("due");
        $invoice = \WHMCS\Billing\Invoice::newInvoice($clientId, $gateway);
        if($invoiceDate) {
            $invoice->dateCreated = \WHMCS\Carbon::createFromAdminDateFormat($invoiceDate);
        }
        if($dueDate) {
            $invoice->dateDue = \WHMCS\Carbon::createFromAdminDateFormat($dueDate);
        }
        $invoice->save();
        $redirectUrl = \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl() . "/invoices.php?action=edit&id=" . $invoice->id;
        return new \WHMCS\Http\Message\JsonResponse(["redirect" => $redirectUrl]);
    }
    public function gatewayBalancesTotals(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        return new \WHMCS\Http\Message\JsonResponse(["success" => true, "body" => \WHMCS\Gateways::gatewayBalancesTotalsView((bool) $request->get("force"))]);
    }
    public function transactionInformation(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        if(!function_exists("getClientsDetails")) {
            require_once ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "clientfunctions.php";
        }
        $id = $request->get("id");
        try {
            $transaction = \WHMCS\Billing\Payment\Transaction::with(["client", "invoice"])->findOrFail($id);
            $gateway = $transaction->paymentGateway;
            if(!$gateway) {
                throw new \WHMCS\Exception("Invalid Request");
            }
            $gatewayInterface = \WHMCS\Module\Gateway::factory($gateway);
            if(!$gatewayInterface->functionExists("TransactionInformation")) {
                throw new \WHMCS\Exception\Module\NotServicable("Transaction information not supported for gateway.");
            }
            if($transaction->client) {
                $client = $transaction->client;
            } else {
                $client = $transaction->invoice->client;
            }
            $transactionInformation = $gatewayInterface->call("TransactionInformation", ["transactionId" => $transaction->transactionId, "clientdetails" => getClientsDetails($client)]);
            $vars = ["errorMessage" => NULL, "transaction" => $transaction, "transactionInformation" => $transactionInformation, "gatewayInterface" => $gatewayInterface];
        } catch (\WHMCS\Exception\Module\NotServicable $e) {
            $vars = ["errorMessage" => $e->getMessage()];
        } catch (\WHMCS\Exception\Fatal $e) {
            $vars = ["errorMessage" => "Inactive or Missing Gateway"];
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $vars = ["errorMessage" => "Invalid Transaction"];
        } catch (\Throwable $t) {
            $vars = ["errorMessage" => $t->getMessage()];
        }
        $body = view("admin.billing.transaction.information", $vars);
        return new \WHMCS\Http\Message\JsonResponse(["body" => $body]);
    }
    public function viewInvoice(\WHMCS\Http\Message\ServerRequest $request)
    {
        if(!function_exists("paymentMethodsSelection")) {
            \App::load_function("gateway");
        }
        $invoiceId = (int) $request->getAttribute("invoiceId");
        $invoice = \WHMCS\Billing\Invoice::with(["client", "client.defaultBillingContact", "items", "transactions", "paidAffiliateCommissions", "paidAffiliateCommissions.affiliate"])->find($invoiceId);
        if(!$invoice) {
            throw new \WHMCS\Payment\Exception\InvalidModuleException("Invalid Access Attempt");
        }
        $clientId = $invoice->clientId;
        if($invoice->isProformaInvoice()) {
            $pageTitle = \AdminLang::trans("fields.proformaInvoiceNum") . $invoice->getInvoiceNumber();
        } else {
            $pageTitle = \AdminLang::trans("fields.invoicenum") . $invoice->getInvoiceNumber();
        }
        $aInt = new \WHMCS\Admin("loginonly");
        $aInt->title = $pageTitle;
        $aInt->sidebar = "billing";
        $aInt->icon = "invoices";
        $aInt->valUserID($clientId);
        $aInt->addContentAreaClass("view-invoice");
        $aInt->assertClientBoundary($clientId)->setResponseType(\WHMCS\Admin::RESPONSE_HTML_MESSAGE);
        $csrfToken = generate_token("plain");
        $refundPermission = $aInt->hasPermission("Refund Invoice Payments");
        $repopulateData = \WHMCS\Cookie::get("ValidationError", true);
        $failedAddPaymentData = [];
        $validationErrorMessage = "";
        if($repopulateData) {
            foreach ($repopulateData["validationError"] as $validationError) {
                $validationErrorMessage .= \WHMCS\Input\Sanitize::makeSafeForOutput($validationError) . "<br />";
            }
            if($validationErrorMessage) {
                $validationErrorMessage = \WHMCS\View\Helper::alert(\AdminLang::trans("global.validationerror") . "<br>" . $validationErrorMessage, "danger");
            }
            $failedAddPaymentData = $repopulateData["submission"];
        }
        \WHMCS\Cookie::delete("ValidationError");
        $gatewayInterface = $invoice->getGatewayInterface();
        $adminTabs = [\AdminLang::trans("invoices.summary"), \AdminLang::trans("invoices.addpayment"), \AdminLang::trans("fields.credit"), \AdminLang::trans("invoices.refund"), \AdminLang::trans("fields.notes")];
        $captureButtonText = \AdminLang::trans("invoices.attemptcapture");
        $captureDisabled = "";
        if($gatewayInterface->functionExists("initiatepayment")) {
            $captureButtonText = \AdminLang::trans("invoices.initiatepayment");
        }
        if(in_array($invoice->status, [\WHMCS\Utility\Status::CANCELLED, \WHMCS\Utility\Status::DRAFT, \WHMCS\Utility\Status::PAID, \WHMCS\Utility\Status::REFUNDED]) || !$gatewayInterface->functionExists("capture") || $invoice->paymentGateway === "offlinecc" || $invoice->balance <= 0) {
            $captureDisabled = " disabled=\"disabled\"";
        }
        $payMethodOutput = "";
        if($invoice->payMethod) {
            $payMethodGateway = $invoice->payMethod->getGateway();
            if($payMethodGateway && $payMethodGateway->getDisplayName() === $invoice->paymentGatewayName) {
                $payMethodOutput .= " - " . $invoice->payMethod->payment->getDisplayName();
            }
        }
        $lastCaptureAttempt = "";
        if($invoice->status === \WHMCS\Utility\Status::UNPAID && $gatewayInterface->getParam("type") == $gatewayInterface::GATEWAY_CREDIT_CARD) {
            $lastCaptureDate = \AdminLang::trans("global.none");
            $lastCaptureAttemptTitle = \AdminLang::trans("fields.lastCaptureAttempt");
            if($invoice->getRawAttribute("last_capture_attempt") !== "0000-00-00 00:00:00") {
                $lastCaptureDate = $invoice->lastCaptureAttempt->toAdminDateFormat();
            }
            $lastCaptureAttempt .= "<br>" . $lastCaptureAttemptTitle . ": <b>" . $lastCaptureDate . "</b>";
        }
        \WHMCS\Session::start();
        $flash = \WHMCS\FlashMessages::get();
        if(isset($flash["type"]) && $flash["type"] === "error") {
            $flash["type"] = "danger";
        }
        \WHMCS\Session::release();
        $transactionTableData = $this->getTransactionTableData($invoice);
        $transactionHistoryTableData = $this->getTransactionHistoryTableData($invoice);
        $affiliateHistoryTableData = $this->getAffiliateHistoryTableData($invoice);
        $downloadUrl = \WHMCS\Utility\Environment\WebHelper::getBaseUrl() . "/dl.php?type=i&id=" . $invoice->id;
        $printUrl = $downloadUrl . "&viewpdf=1";
        $langParam = "&language=" . \AdminLang::getName();
        $clientInvoiceLink = \WHMCS\Utility\Environment\WebHelper::getBaseUrl() . "/viewinvoice.php?id=" . $invoiceId . "&view_as_client=1";
        $adminLanguage = ucfirst(\AdminLang::getName());
        $clientLang = ucfirst(\Lang::getValidLanguageName($invoice->getModel()->client->language ?: \Lang::getDefault()));
        $refundCheckboxText = "";
        $refundLabelText = "";
        $refundOnSubmit = "";
        $refundRadioOptions = [];
        $refundTransactions = [];
        $refundWarning = "";
        $invoiceCredit = 0;
        if($refundPermission) {
            $refundTransactions = $invoice->transactions()->with("refunds")->where("amountin", ">", "0")->get();
            $transAmountObjectTxt = "";
            foreach ($refundTransactions as $i => $transaction) {
                if($transaction->refunds()->count()) {
                    $amountRefunded = $transaction->refunds()->sum("amountout");
                    if($transaction->amountIn <= $amountRefunded) {
                        $refundTransactions->forget($i);
                    }
                }
                $id = $transaction->id;
                $amount = $transaction->amountIn;
                $transAmountObjectTxt .= "       transAmountObj._" . $id . " = " . $amount . ";" . PHP_EOL;
            }
            $invoiceCredit = \WHMCS\Database\Capsule::table("tblcredit")->where("relid", $invoice->id)->sum("amount");
            $aInt->jscode .= "function selectRefundChoice(selection)\n{\n    if (selection.checked) {\n        // A choice was made.\n        // Enable the refund button.\n        \$('#refundBtn').removeAttr('disabled');\n    } else {\n        // Checkbox was unchecked.\n        // Disable the refund button.\n        \$('#refundBtn').prop('disabled', 'disabled');\n    }\n}\nfunction showRefundTransactionId() {\n    var refundType = \$('#refundType').val();\n    if (refundType !== '') {\n        \$('#refundTransactionId').slideUp();\n    } else {\n        \$('#refundTransactionId').slideDown();\n    }\n}";
            $aInt->jquerycode .= "\$('#transactions').submit(function (e) {\n    var credit = '" . $invoiceCredit . "',\n        choice = \$('input[id^=warning]:checked', '#transactions').val();\n    \n    if (credit > 0 && choice !== 'leaveCredit') {\n        var amount = \$('#amount').val(),\n            selectedId = '_' . \$('#transactionId').find('option:selected').val(),\n            transAmountObject = {},\n            removeCreditAmount;\n        \n        " . $transAmountObjectTxt . "\n        var transAmount = transAmountObject[selectedId];\n        amount = amount.replace(/^\\s*/, '').replace(/\\s&\$/, '');\n        \n        if (amount === '') {\n            amount = transAmount;\n        }\n        \n        if (amount < credit) {\n            removeCreditAmount = amount;\n        } else if (amount >= credit) {\n            removeCreditAmount = credit;\n        } else {\n            return;\n        }\n        \$('#invoiceCredit').val(removeCreditAmount);\n    }\n});";
            if($affiliateHistoryTableData) {
                $refundOnSubmit = " onsubmit=\"reverseCommissionConfirm(" . ($invoice->credit + $invoice->total) . ", " . $invoice->balance . "); return false;\"";
            }
            if(0 < $invoiceCredit) {
                $totalCredit = $invoice->client->credit;
                $refundWarning = \AdminLang::trans("invoices.invoiceCreditResult");
                $refundWarning .= formatCurrency($invoiceCredit, $invoice->client->currencyId) . ".<br>";
                $refundWarning .= \AdminLang::trans("invoices.currentCreditBalance");
                $refundWarning .= formatCurrency($totalCredit, $invoice->client->currencyId);
                if($totalCredit < $invoiceCredit) {
                    $refundCheckboxText = "<strong>" . \AdminLang::trans("invoices.cannotRemoveCreditAck") . "</strong>";
                    $refundLabelText = \AdminLang::trans("invoices.cannotRemoveCredit");
                } else {
                    $refundLabelText = \AdminLang::trans("invoices.creditCanBeRemoved");
                    $refundRadioOptions = ["removeCredit" => "<strong>" . \AdminLang::trans("invoices.removeCreditFirst") . "</strong>", "leaveCredit" => "<strong>" . \AdminLang::trans("invoices.leaveCreditUntouched") . "</strong>"];
                }
            }
        }
        $paymentMethod = $invoice->paymentGateway;
        if($failedAddPaymentData) {
            $paymentMethod = $failedAddPaymentData["paymentmethod"];
        }
        $paymentMethodDropDown = paymentMethodsSelection(\AdminLang::trans("global.none"), false, $paymentMethod);
        $addPaymentDate = $failedAddPaymentData["date"] ?? \WHMCS\Carbon::today()->toAdminDateFormat();
        $addPaymentBalance = $failedAddPaymentData["amount"] ?? $invoice->balance;
        $addPaymentFees = $failedAddPaymentData["fees"] ?? "0.00";
        $addPaymentTransactionId = $failedAddPaymentData["transid"] ?? "";
        $addPaymentSendConfirmationChecked = !$failedAddPaymentData || $failedAddPaymentData["sendconfirmation"] ? " checked=\"checked\" " : "";
        $aInt->jscode .= "var thisInvoiceId = " . $invoice->id . ";";
        $aInt->jquerycode .= "jQuery('#selectPaymentGateway').on('change', function (e) {\n    jQuery('#gatewayLoading').show();\n    var post = WHMCS.http.jqClient.post(\n        WHMCS.adminUtils.getAdminRouteUrl('/billing/invoice/' + thisInvoiceId + '/change-gateway'),\n        {\n            token: '" . $csrfToken . "',\n            gateway: jQuery(this).val()\n        }\n    );\n    \n    post.done(function (data) {\n        if (data.success === false) {\n            jQuery.growl.warning({title: '', message: data.message});\n        } else {\n            jQuery.growl.notice({title: '', message: data.message});\n        }\n    });\n    post.always(function () {\n        jQuery('#gatewayLoading').hide();\n    })\n});";
        $currencyStep = 0;
        if($invoice->client->currencyrel->format === 4) {
            $currencyStep = 1;
        }
        $taxEnabled = \WHMCS\Config\Setting::getValue("TaxEnabled");
        $taxData = [];
        $taxData2 = [];
        $clientBillingData = $invoice->client->defaultBillingContact;
        if($taxEnabled) {
            $taxData = getTaxRate(1, $clientBillingData->state, $clientBillingData->country);
            $taxData2 = getTaxRate(2, $clientBillingData->state, $clientBillingData->country);
        }
        $aInt->content = view("admin.billing.invoice.view", ["addPaymentDate" => $addPaymentDate, "addPaymentBalance" => format_as_currency($addPaymentBalance), "addPaymentFees" => $addPaymentFees, "addPaymentPermission" => $aInt->hasPermission("Add Transaction"), "addPaymentTransactionId" => $addPaymentTransactionId, "addPaymentSendConfirmationChecked" => $addPaymentSendConfirmationChecked, "adminLanguage" => $adminLanguage, "affiliateHistoryTableData" => $affiliateHistoryTableData, "aInt" => $aInt, "availablePaymentGateways" => \WHMCS\Module\GatewaySetting::getActiveGatewayFriendlyNames(), "captureButtonText" => $captureButtonText, "captureDisabled" => $captureDisabled, "clientCredit" => $invoice->client->credit, "clientId" => $clientId, "clientInvoiceLink" => $clientInvoiceLink, "clientLang" => $clientLang, "creditGiven" => 0 < $invoiceCredit, "csrfToken" => $csrfToken, "csrfLinkToken" => generate_token("link"), "currencyStep" => $currencyStep, "downloadUrl" => $downloadUrl, "emailTemplates" => $this->getEmailTemplateList($invoice->status), "flash" => $flash, "gatewayInterface" => $gatewayInterface, "invoice" => $invoice, "invoiceCredit" => $invoiceCredit, "langParam" => $langParam, "lastCaptureAttempt" => $lastCaptureAttempt, "manageInvoicePermission" => $aInt->hasPermission("Manage Invoice"), "paymentMethod" => $paymentMethod, "paymentMethodDropDown" => $paymentMethodDropDown, "payMethodOutput" => $payMethodOutput, "printUrl" => $printUrl, "refundCheckboxText" => $refundCheckboxText, "refundLabelText" => $refundLabelText, "refundOnSubmit" => $refundOnSubmit, "refundPermission" => $refundPermission, "refundRadioOptions" => $refundRadioOptions, "refundTransactions" => $refundTransactions, "refundWarning" => $refundWarning, "sendEmailDisabled" => in_array($invoice->status, [\WHMCS\Utility\Status::DRAFT, \WHMCS\Utility\Status::CANCELLED]), "status" => $invoice->status, "statusClass" => \WHMCS\View\Helper::generateCssFriendlyClassName($invoice->status), "tabs" => $adminTabs, "taxData" => $taxData, "taxData2" => $taxData2, "taxEnabled" => $taxEnabled, "transactionTableData" => $transactionTableData, "transactionHistoryTableData" => $transactionHistoryTableData, "validationErrorMessage" => $validationErrorMessage, "webroot" => \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl()]);
        return $aInt->display();
    }
    public function addInvoicePayment(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        $invoiceId = (int) $request->getAttribute("invoiceId");
        $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
        if(!$invoice) {
            throw new \WHMCS\Payment\Exception\InvalidModuleException("Invalid Access Attempt");
        }
        $fromView = $request->has("view");
        $transactionId = $request->get("transid");
        $amount = $request->get("amount", 0);
        $fees = $request->get("fees", 0);
        $paymentMethod = $request->get("paymentmethod");
        $sendConfirmation = (bool) $request->get("sendconfirmation", 0);
        $date = $request->get("date");
        $validationError = false;
        $validationErrorDescription = [];
        if($amount < 0) {
            $validationError = true;
            $validationErrorDescription[] = \AdminLang::trans("transactions.amountInLessThanZero") . PHP_EOL;
        }
        if((!$amount || $amount == 0) && (!$fees || $fees == 0)) {
            $validationError = true;
            $validationErrorDescription[] = \AdminLang::trans("transactions.amountOrFeeRequired") . PHP_EOL;
        }
        $validate = new \WHMCS\Validate();
        $invalidFormatLangKey = ["transactions", "amountOrFeeInvalidFormat"];
        if($amount && !$validate->validate("decimal", "amount", $invalidFormatLangKey) || $fees && !$validate->validate("decimal", "fees", $invalidFormatLangKey)) {
            $validationError = true;
            $validationErrorDescription[] = implode(PHP_EOL, array_unique($validate->getErrors())) . PHP_EOL;
        }
        if($amount && $fees && $amount < $fees) {
            $validationError = true;
            $validationErrorDescription[] = \AdminLang::trans("transactions.feeMustBeLessThanAmountIn") . PHP_EOL;
        }
        if($amount && $fees && $fees < 0) {
            $validationError = true;
            $validationErrorDescription[] = \AdminLang::trans("transactions.amountInFeeMustBePositive") . PHP_EOL;
        }
        $validationURL = "";
        $validationQueryParameters = [];
        if(!$validationError) {
            $date = !empty($date) ? \WHMCS\Carbon::createFromAdminDateFormat($date)->setTimeNow() : NULL;
            $invoice->addPayment($amount, $transactionId, $fees, $paymentMethod, !$sendConfirmation, $date);
        } else {
            \WHMCS\Cookie::set("ValidationError", ["validationError" => $validationErrorDescription, "submission" => ["transid" => $transactionId, "amount" => $amount, "fees" => $fees, "paymentmethod" => $paymentMethod, "sendconfirmation" => $sendConfirmation, "date" => $date]]);
            if($fromView) {
                $validationQueryParameters = ["error" => "validation", "tab" => 2];
            } else {
                $validationURL = "&error=validation&tab=2";
            }
        }
        $redirectUri = "invoices.php?action=edit&id=" . $invoiceId . $validationURL;
        if($fromView) {
            $redirectUri = routePathWithQuery("admin-billing-view-invoice", [$invoiceId], $validationQueryParameters);
        }
        return new \WHMCS\Http\Message\JsonResponse(["redirectUri" => $redirectUri]);
    }
    public function changeGateway(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        $invoiceId = (int) $request->getAttribute("invoiceId");
        $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
        if(!$invoice) {
            return new \WHMCS\Http\Message\JsonResponse(["success" => false, "message" => "Invalid Access Attempt"]);
        }
        $gateway = $request->get("gateway");
        $availableGateways = \WHMCS\Module\GatewaySetting::getActiveGatewayFriendlyNames();
        if(!$gateway || !in_array($gateway, array_keys($availableGateways))) {
            return new \WHMCS\Http\Message\JsonResponse(["success" => false, "message" => "Invalid gateway selected"]);
        }
        $invoice->paymentGateway = $gateway;
        $invoice->save();
        \HookMgr::run("InvoiceChangeGateway", ["invoiceid" => $invoice->id, "paymentmethod" => $gateway]);
        logActivity("Modified Invoice Gateway - Invoice ID: " . $invoice->id, $invoice->clientId);
        return new \WHMCS\Http\Message\JsonResponse(["success" => true, "message" => \AdminLang::trans("global.changesuccessdesc")]);
    }
    public function checkTransactionId(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        if(!function_exists("isUniqueTransactionID")) {
            \App::load_function("invoice");
        }
        $transactionId = $request->get("transaction_id");
        $paymentMethod = $request->get("payment_method");
        $unique = true;
        if($transactionId && $paymentMethod) {
            $unique = isUniqueTransactionID($transactionId, $paymentMethod);
        }
        return new \WHMCS\Http\Message\JsonResponse(["unique" => $unique]);
    }
    public function sendEmail(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\RedirectResponse
    {
        $templateName = $request->get("template");
        $invoiceId = (int) $request->getAttribute("invoiceId");
        $result = sendMessage($templateName, $invoiceId);
        $response = new \WHMCS\Http\RedirectResponse(routePath("admin-billing-view-invoice", $invoiceId));
        if($result === true) {
            $invoice = \WHMCS\Billing\Invoice::with("client")->find($invoiceId);
            $clientLink = $invoice->client->getLink();
            $clientName = \WHMCS\Input\Sanitize::makeSafeForOutput($invoice->client->fullName);
            return $response->withSuccess(\AdminLang::trans("email.sentSuccessfullyTo", [":entityName" => "<a href=\"" . $clientLink . "\">" . $clientName . "</a>"]));
        }
        return $response->withError($result);
    }
    public function viewInvoiceRefundPayment(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\RedirectResponse
    {
        if(!function_exists("refundInvoicePayment")) {
            \App::load_function("invoice");
        }
        if(!function_exists("getCCVariables")) {
            \App::load_function("cc");
        }
        $invoiceId = $request->getAttribute("invoiceId");
        $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
        $transactionId = $request->get("transaction_id");
        $amount = $request->get("amount");
        $sendEmail = (bool) $request->get("sendemail");
        $refundTransactionId = $request->get("refund_transaction_id");
        $refundType = $request->get("refund_type");
        $reverse = (bool) (int) $request->get("reverse", 0);
        $reverseCommission = $request->get("reverseCommission");
        $invoiceCredit = $request->get("invoice_credit");
        logActivity("Admin Initiated Refund - Invoice ID: " . $invoiceId . " - Transaction ID: " . $transactionId);
        $sendToGateway = false;
        $addAsCredit = false;
        $commissionReversed = false;
        if($refundType == "sendtogateway") {
            $sendToGateway = true;
        } elseif($refundType == "addascredit") {
            $addAsCredit = true;
        }
        $result = refundInvoicePayment($transactionId, $amount, $sendToGateway, $addAsCredit, $sendEmail, $refundTransactionId, $reverse, $reverseCommission, $commissionReversed);
        $redirect = new \WHMCS\Http\RedirectResponse(routePath("admin-billing-view-invoice", $invoiceId));
        $refundSuccess = true;
        switch ($result) {
            case "manual":
                $infoBoxTitle = \AdminLang::trans("invoices.refundsuccess");
                $infoBoxDescription = \AdminLang::trans("invoices.refundmanualsuccessmsg");
                break;
            case "success":
                $infoBoxTitle = \AdminLang::trans("invoices.refundsuccess");
                $infoBoxDescription = \AdminLang::trans("invoices.refundsuccessmsg");
                break;
            case "creditsuccess":
                $infoBoxTitle = \AdminLang::trans("invoices.refundsuccess");
                $infoBoxDescription = \AdminLang::trans("invoices.refundcreditmsg");
                break;
            case "amounterror":
            default:
                $refundSuccess = false;
                $infoBoxTitle = \AdminLang::trans("invoices.refundfailed");
                $infoBoxDescription = \AdminLang::trans("invoices.refundfailedmsg");
                $message = "<strong>" . $infoBoxTitle . "</strong>";
                $message .= "<br>" . $infoBoxDescription;
                if($refundSuccess) {
                    removeOverpaymentCredit($invoice->clientId, $transactionId, $invoiceCredit);
                    if(in_array($result, ["manual", "success"]) && $commissionReversed) {
                        $message .= "<br>" . \AdminLang::trans("affiliates.reverseCommissionSuccess");
                    }
                    return $redirect->withSuccess($message);
                }
                return $redirect->withError($message);
        }
    }
    public function addCredit(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\RedirectResponse
    {
        $invoiceId = $request->getAttribute("invoiceId");
        $addCredit = $request->get("addcredit");
        $fromView = $request->has("view");
        $invoice = \WHMCS\Billing\Invoice::with("client")->find($invoiceId);
        if(!$invoice) {
            throw new \WHMCS\Payment\Exception\InvalidModuleException("Invalid Access Attempt");
        }
        $balance = $invoice->balance;
        $addCredit = round($addCredit, 2);
        $balance = round($balance, 2);
        $totalCredit = $invoice->client->credit;
        if(!$fromView) {
            $redirectUrl = \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl();
            $redirectUrl = $redirectUrl . "/invoices.php?action=edit&id=" . $invoice->id;
            define("ROUTE_REDIRECT_TO_LEGACY", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl() . "/invoices.php");
        } else {
            $redirectUrl = routePath("admin-billing-view-invoice", $invoice->id);
        }
        $redirect = new \WHMCS\Http\RedirectResponse($redirectUrl);
        if($totalCredit < $addCredit) {
            return $redirect->withError(\AdminLang::trans("invoices.exceedBalance"));
        }
        if($balance < $addCredit) {
            return $redirect->withError(\AdminLang::trans("invoices.exceedTotal"));
        }
        $invoice->applyCredit($addCredit);
        $message = sprintf(\AdminLang::trans("invoices.creditApplySuccess"), formatCurrency($addCredit, $invoice->client->currencyId));
        return $redirect->withSuccess($message);
    }
    public function removeCredit(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\RedirectResponse
    {
        $invoiceId = $request->getAttribute("invoiceId");
        $removeCredit = $request->get("removecredit");
        $fromView = $request->has("view");
        $invoice = \WHMCS\Billing\Invoice::with("client")->find($invoiceId);
        if(!$invoice) {
            throw new \WHMCS\Payment\Exception\InvalidModuleException("Invalid Access Attempt");
        }
        $credit = $invoice->credit;
        $removeCredit = round($removeCredit, 2);
        if(!$fromView) {
            $redirectUrl = \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl();
            $redirectUrl = $redirectUrl . "/invoices.php?action=edit&id=" . $invoice->id;
            define("ROUTE_REDIRECT_TO_LEGACY", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl() . "/invoices.php");
        } else {
            $redirectUrl = routePath("admin-billing-view-invoice", $invoice->id);
        }
        $redirect = new \WHMCS\Http\RedirectResponse($redirectUrl);
        if($credit < $removeCredit) {
            return $redirect->withError(\AdminLang::trans("invoices.exceedTotalRemove"));
        }
        $invoice->removeCredit($removeCredit);
        $message = sprintf(\AdminLang::trans("invoices.creditRemoveSuccess"), formatCurrency($removeCredit, $invoice->client->currencyId));
        return $redirect->withSuccess($message);
    }
    public function viewInvoiceTooltip(\WHMCS\Http\Message\ServerRequest $request)
    {
        $invoiceId = $request->getAttribute("invoiceId");
        $invoice = \WHMCS\Billing\Invoice::with("items")->find($invoiceId);
        if(!$invoice) {
            throw new \WHMCS\Payment\Exception\InvalidModuleException("Invalid Access Attempt");
        }
        $amountText = \AdminLang::trans("fields.amount");
        $creditText = \AdminLang::trans("fields.credit");
        $descriptionText = \AdminLang::trans("fields.description");
        $subtotalText = \AdminLang::trans("fields.subtotal");
        $taxText = \AdminLang::trans("fields.tax");
        $totalText = \AdminLang::trans("fields.totaldue");
        $greyRowStart = "<tr bgcolor=\"#efefef\" style=\"text-align:right;font-weight:bold;\">";
        $currency = getCurrency($invoice->clientId);
        $tableRows = "";
        foreach ($invoice->items as $item) {
            $description = nl2br($item->description);
            $amount = formatCurrency($item->amount, $invoice->client->currencyId);
            $tableRows .= "<tr bgcolor=\"#ffffff\"><td width=\"275\">" . $description . "</td>" . "<td width=\"100\" style=\"text-align:right;\">" . $amount . "</tr>";
        }
        $subtotal = formatCurrency($invoice->subtotal, $invoice->client->currencyId);
        $tableRows .= $greyRowStart . "<td>" . $subtotalText . "&nbsp;</td><td>" . $subtotal . "</td>";
        if(\WHMCS\Config\Setting::getValue("TaxEnabled")) {
            if(0 < $invoice->tax1) {
                $tax = formatCurrency($invoice->tax1, $invoice->client->currencyId);
                $tableRows .= $greyRowStart . "<td>" . $invoice->taxRate1 . "% " . $taxText . "&nbsp;</td><td>" . $tax . "</td></tr>";
            }
            if(0 < $invoice->tax2) {
                $tax = formatCurrency($invoice->tax2, $invoice->client->currencyId);
                $tableRows .= $greyRowStart . "<td>" . $invoice->taxRate2 . "% " . $taxText . "&nbsp;</td><td>" . $tax . "</td></tr>";
            }
        }
        $credit = formatCurrency($invoice->credit, $invoice->client->currencyId);
        $total = formatCurrency($invoice->total, $invoice->client->currencyId);
        return new \WHMCS\Http\Message\JsonResponse(["body" => "<table bgcolor=\"#cccccc\" cellspacing=\"1\" cellpadding=\"3\">\n    <tr bgcolor=\"#efefef\" style=\"text-align:center;font-weight:bold;\">\n        <td>" . $descriptionText . "</td>\n        <td>" . $amountText . "</td>\n    </tr>\n    " . $tableRows . "\n    " . $greyRowStart . "\n        <td>" . $creditText . "&nbsp;</td>\n        <td>" . $credit . "</td>\n    </tr>\n    " . $greyRowStart . "\n        <td>" . $totalText . "&nbsp;</td>\n        <td>" . $total . "</td>\n    </tr>\n</table>"]);
    }
    protected function getEmailTemplateList($status) : array
    {
        $emailTemplateArray = \WHMCS\Mail\Template::where("type", \WHMCS\Mail\Emailer::EMAIL_TYPE_INVOICE)->where("language", "")->pluck("id", "name")->toArray();
        $emailTemplateOutput = ["Invoice Created", "Credit Card Invoice Created", "Invoice Payment Reminder", "First Invoice Overdue Notice", "Second Invoice Overdue Notice", "Third Invoice Overdue Notice", "Credit Card Payment Due", "Credit Card Payment Failed", "Invoice Payment Confirmation", "Credit Card Payment Confirmation", "Invoice Refund Confirmation"];
        if($status == \WHMCS\Utility\Status::PAID) {
            $emailTemplateOutput = array_merge(["Invoice Payment Confirmation", "Credit Card Payment Confirmation"], $emailTemplateOutput);
        }
        if($status == \WHMCS\Utility\Status::REFUNDED) {
            $emailTemplateOutput = array_merge(["Invoice Refund Confirmation"], $emailTemplateOutput);
        }
        $emailTemplates = [];
        foreach ($emailTemplateOutput as $templateName) {
            if(array_key_exists($templateName, $emailTemplateArray)) {
                $emailTemplates[] = $templateName;
                unset($emailTemplateArray[$templateName]);
            }
        }
        foreach ($emailTemplateArray as $templateName => $k) {
            $emailTemplates[] = $templateName;
        }
        return $emailTemplates;
    }
    protected function getTransactionTableData(\WHMCS\Billing\Invoice $invoice) : array
    {
        $paymentGateways = new \WHMCS\Gateways();
        $transactionTableData = [];
        $transactions = [];
        foreach ($invoice->transactions as $transaction) {
            $paymentmethod = "";
            if($transaction->paymentGateway) {
                $paymentmethod = $paymentGateways->getDisplayName($transaction->paymentGateway);
            }
            if(!$paymentmethod) {
                $paymentmethod = "-";
            }
            $transactions[(string) $transaction->date][] = [$transaction->date->toAdminDateFormat(), $paymentmethod, $transaction->getTransactionIdMarkup(), formatCurrency($transaction->amountIn - $transaction->amountOut, $invoice->client->currencyId), formatCurrency($transaction->fees, $invoice->client->currencyId)];
        }
        $creditTransactions = \WHMCS\Database\Capsule::table("tblcredit")->where("description", "LIKE", "%Invoice #" . $invoice->id)->get()->all();
        $creditRemoved = \AdminLang::trans("invoices.creditRemoved");
        $creditApplied = \AdminLang::trans("invoices.creditApplied");
        foreach ($creditTransactions as $transaction) {
            if(0 < $transaction->amount) {
                $isOverpayment = strpos($transaction->description, "Overpayment");
                $isMassPayment = strpos($transaction->description, "Mass Invoice Payment Credit");
                if($isOverpayment !== false || $isMassPayment !== false) {
                } else {
                    $creditMsg = $creditRemoved;
                }
            } else {
                $creditMsg = $creditApplied;
            }
            $transactions[$transaction->date . " 25:59:59"][] = [fromMySQLDate($transaction->date), $creditMsg, "-", formatCurrency($transaction->amount * -1, $invoice->client->currencyId), "-"];
        }
        ksort($transactions);
        foreach ($transactions as $trans) {
            foreach ($trans as $transaction) {
                $transactionTableData[] = $transaction;
            }
        }
        return $transactionTableData;
    }
    protected function getTransactionHistoryTableData(\WHMCS\Billing\Invoice $invoice) : array
    {
        $paymentGateways = new \WHMCS\Gateways();
        $transactionHistoryTableData = [];
        $transHistTooltip = \AdminLang::trans("invoices.transactionsHistoryTooltip");
        foreach ($invoice->transactionHistory as $transactionHistory) {
            $transHistTransIdLink = "<a href=\"gatewaylog.php?history=" . $transactionHistory->id . "\">\n" . $transactionHistory->transactionId . "\n<i data-toggle=\"tooltip\"\n   data-container=\"body\"\n   data-placement=\"right auto\"\n   data-trigger=\"hover\"\n   class=\"fal fa-line-columns\"\n   title=\"" . $transHistTooltip . "\"\n></i>\n</a>";
            $transactionHistoryTableData[] = [$transactionHistory->updatedAt->toAdminDateTimeFormat(), $paymentGateways->getDisplayName($transactionHistory->gateway), $transHistTransIdLink, $transactionHistory->remoteStatus, $transactionHistory->description];
        }
        return $transactionHistoryTableData;
    }
    protected function getAffiliateHistoryTableData(\WHMCS\Billing\Invoice $invoice) : array
    {
        $paidAffiliateCommissions = $invoice->paidAffiliateCommissions->count();
        $pendingAffiliateCommissions = $invoice->pendingAffiliateCommissions()->count();
        $affiliateHistoryTableData = [];
        if($paidAffiliateCommissions || $pendingAffiliateCommissions) {
            foreach ($invoice->pendingAffiliateCommissions as $affiliatePending) {
                $affiliate = $affiliatePending->account->affiliate;
                $affiliateHistoryTableData[] = [$affiliatePending->createdAt->toAdminDateFormat(), "<a href=\"" . $affiliate->getFullAdminUrl() . "\" class=\"autoLinked\">" . $affiliate->client->fullName . "</a>", formatCurrency($affiliatePending->amount, $affiliate->client->currencyId), \AdminLang::trans("affiliates.pendingCommissionWillClear", [":clearDate" => $affiliatePending->clearingDate->toAdminDateFormat()])];
            }
            foreach ($invoice->paidAffiliateCommissions as $affiliateHistory) {
                $affiliate = $affiliateHistory->affiliate;
                $affiliateHistoryTableData[] = [$affiliateHistory->date->toAdminDateFormat(), "<a href=\"" . $affiliate->getFullAdminUrl() . "\" class=\"autoLinked\">" . $affiliate->client->fullName . "</a>", formatCurrency($affiliateHistory->amount, $affiliate->client->currencyId), $affiliateHistory->description];
            }
        }
        return $affiliateHistoryTableData;
    }
}

?>