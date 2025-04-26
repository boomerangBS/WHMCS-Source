<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class CreateOrderRequest extends AbstractRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $intent;
    protected $purchaseUnit = [];
    protected $paymentSource = [];
    public function send() : HttpResponse
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
        return !empty($this->intent) && !empty($this->purchaseUnit);
    }
    public function responseType() : AbstractResponse
    {
        return new CreateOrderResponse();
    }
    public function setPurchaseUnit($description, string $invoiceId, string $amountValue, string $amountCurrencyCode) : \self
    {
        $this->purchaseUnit = [["description" => $description, "custom_id" => $invoiceId, "invoice_id" => $invoiceId, "amount" => ["value" => $amountValue, "currency_code" => $amountCurrencyCode]]];
        return $this;
    }
    public function setPaymentSource(Entity\AbstractPaymentSource $paymentSource) : \self
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