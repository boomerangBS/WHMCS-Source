<?php

namespace WHMCS\Module\Gateway\paypal_acdc\API;

class CreateOrderRequest extends \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractRequest
{
    use \WHMCS\Module\Gateway\paypal_ppcpv\API\RequestAccessTokenAuthenticatedTrait;
    protected $intent = "";
    protected $purchaseUnit = [];
    protected $paymentSource = [];
    public function send() : \WHMCS\Module\Gateway\paypal_ppcpv\API\HttpResponse
    {
        return $this->partnerAttribution()->contentJSON()->acceptJSON()->post("/v2/checkout/orders", $this->payload());
    }
    public function payload()
    {
        $payload = ["intent" => $this->intent, "purchase_units" => $this->purchaseUnit];
        if(!empty($this->paymentSource)) {
            $payload["payment_source"] = $this->paymentSource;
        }
        return json_encode($payload);
    }
    public function sendReady()
    {
        return $this->intent != "" && !empty($this->purchaseUnit);
    }
    public function responseType() : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderResponse();
    }
    public function setPurchaseUnit($description, string $invoiceId, string $amountValue, string $amountCurrencyCode) : \self
    {
        $this->purchaseUnit = [["description" => $description, "custom_id" => $invoiceId, "invoice_id" => $invoiceId, "amount" => ["value" => $amountValue, "currency_code" => $amountCurrencyCode]]];
        return $this;
    }
    public function setPaymentSource(\WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\AbstractPaymentSource $paymentSource) : \self
    {
        $this->paymentSource = $paymentSource->get();
        return $this;
    }
    public function setAsCapture() : \self
    {
        $this->intent = "CAPTURE";
        return $this;
    }
    public function setAsAuthorize() : \self
    {
        $this->intent = "AUTHORIZE";
        return $this;
    }
}

?>