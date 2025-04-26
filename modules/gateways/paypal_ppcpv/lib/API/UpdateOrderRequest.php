<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class UpdateOrderRequest extends AbstractRequest
{
    use RequestAccessTokenAuthenticatedTrait;
    protected $orderId = "";
    protected $operations = [];
    private static $purchaseUnitsPathFmt = "/purchase_units/@reference_id=='default'/%s";
    public function send() : HttpResponse
    {
        return $this->partnerAttribution()->contentJSON()->acceptJSON()->patch(sprintf("/v2/checkout/orders/%s", $this->orderId), $this->payload());
    }
    public function payload()
    {
        return json_encode($this->operations);
    }
    public function sendReady()
    {
        return !empty($this->orderId) && !empty($this->operations);
    }
    public function responseType() : AbstractResponse
    {
        return new UpdateOrderResponse();
    }
    public function setOrderIdentifier($orderIdentifier) : \self
    {
        $this->orderId = $orderIdentifier;
        return $this;
    }
    public function replace($path, $value) : \self
    {
        $this->operations[] = ["op" => "replace", "path" => $path, "value" => $value];
        return $this;
    }
    public function add($path, $value) : \self
    {
        $this->operations[] = ["op" => "add", "path" => $path, "value" => $value];
        return $this;
    }
    public function remove($path) : \self
    {
        $this->operations[] = ["op" => "remove", "path" => $path];
        return $this;
    }
    public function updateInvoiceId($invoiceId) : \self
    {
        $this->replace(sprintf(self::$purchaseUnitsPathFmt, "invoice_id"), $invoiceId);
        $this->replace(sprintf(self::$purchaseUnitsPathFmt, "custom_id"), $invoiceId);
        return $this;
    }
    public function updateAmount(string $currencyCode, string $value)
    {
        return $this->replace(sprintf(self::$purchaseUnitsPathFmt, "amount"), ["currency_code" => $currencyCode, "value" => $value]);
    }
}

?>