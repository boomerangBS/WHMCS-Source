<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Client\Invoice;

class InvoiceController
{
    protected $fromView = false;
    public function capture(\WHMCS\Http\Message\ServerRequest $request)
    {
        $clientId = (int) $request->getAttribute("userId");
        $invoiceId = (int) $request->getAttribute("invoiceId");
        $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
        if(!$invoice || $invoice->client->id != $clientId) {
        }
        $client = $invoice->client;
        $client->migratePaymentDetailsIfRequired();
        $payMethods = $client->payMethods()->get();
        $bankGateway = false;
        if($invoice->paymentGateway) {
            $payMethods = $payMethods->forGateway($invoice->paymentGateway);
            if(0 < $payMethods->count()) {
                $payMethod = $payMethods->first();
                if($payMethod && ($payMethod->isBankAccount() || $payMethod->isRemoteBankAccount())) {
                    $bankGateway = true;
                }
            }
        }
        $doCaptureRoute = $this->fromView ? "admin-client-view-invoice-capture-confirm" : "admin-client-invoice-capture-confirm";
        $body = view("admin.client.invoice.capture", ["payMethods" => $payMethods, "client" => $client, "invoice" => $invoice, "viewHelper" => new \WHMCS\Admin\Client\PayMethod\ViewHelper(), "showCvc" => !$bankGateway, "doCaptureRoute" => $doCaptureRoute]);
        $body = (new \WHMCS\Admin\ApplicationSupport\View\PreRenderProcessor())->process($body);
        $response = new \WHMCS\Http\Message\JsonResponse(["body" => $body]);
        return $response;
    }
    public function viewCapture(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        $this->fromView = true;
        return $this->capture($request);
    }
    public function doCapture(\WHMCS\Http\Message\ServerRequest $request)
    {
        try {
            $clientId = (int) $request->getAttribute("userId");
            $invoiceId = (int) $request->getAttribute("invoiceId");
            $payMethodId = (int) $request->get("paymentId");
            $payMethod = \WHMCS\Payment\PayMethod\Model::findForClient($payMethodId, $clientId);
            $invoice = \WHMCS\Billing\Invoice::find($invoiceId);
            if(!$payMethod || $payMethod->client->id != $clientId || !$invoice || $invoice->client->id != $clientId) {
                throw new \WHMCS\Payment\Exception\InvalidModuleException("Invalid Access Attempt");
            }
            if(in_array($invoice->status, ["Paid", "Cancelled"])) {
                throw new \WHMCS\Exception\Validation\InvalidValue("Invalid Status for Capture");
            }
            logActivity("Admin Initiated Payment Capture - Invoice ID: " . $invoice->id, $clientId);
            $success = $payMethod->capture($invoice, $request->request()->get("cardcvv", ""));
            if(is_string($success) && $success == "success" || is_string($success) && $success == "pending" || is_bool($success) && $success) {
                $success = true;
            }
            if($this->fromView) {
                $redirect = routePath("admin-billing-view-invoice", $invoiceId);
            } else {
                $redirect = "invoices.php?action=edit&id=" . $invoiceId;
                define("ROUTE_REDIRECT_TO_LEGACY", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl() . "/invoices.php");
            }
            $paymentGatewayInterface = $invoice->getGatewayInterface();
            $stringPrefix = "capture";
            if($paymentGatewayInterface->functionExists("initiatepayment")) {
                $stringPrefix = "initiatepayment";
            }
            $flashMessage = "invoices." . $stringPrefix . "successfulmsg";
            $flashType = "success";
            if(!$success) {
                $flashMessage = "invoices." . $stringPrefix . "errormsg";
                $flashType = "error";
            }
            \WHMCS\FlashMessages::add(\AdminLang::trans($flashMessage), $flashType);
            $response = new \WHMCS\Http\Message\JsonResponse(["body" => \AdminLang::trans("general.pleaseWait"), "disableSubmit" => true, "dismissLoader" => false, "redirect" => $redirect]);
        } catch (\Exception $e) {
            $body = $e->getMessage();
            $response = new \WHMCS\Http\Message\JsonResponse(["body" => $body]);
        }
        return $response;
    }
    public function viewDoCapture(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse
    {
        $this->fromView = true;
        return $this->doCapture($request);
    }
}

?>