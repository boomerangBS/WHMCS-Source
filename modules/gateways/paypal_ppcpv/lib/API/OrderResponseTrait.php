<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
trait OrderResponseTrait
{
    public $create_time = "";
    public $update_time = "";
    public $id = "";
    public $processing_instruction = "";
    public $links;
    public $payment_source;
    public $intent = "";
    public $payer;
    public $status = "";
    public $purchase_units;
    public function isCaptureComplete()
    {
        return $this->status == "COMPLETED" && $this->captureData()->status == "COMPLETED";
    }
    public function isCapturePending()
    {
        return $this->status == "COMPLETED" && $this->captureData()->status == "PENDING";
    }
    public function isCaptureDeclined()
    {
        return $this->status == "COMPLETED" && $this->captureData()->status == "DECLINED";
    }
    public function isPayerActionRequired()
    {
        return $this->status == "PAYER_ACTION_REQUIRED";
    }
    public function captureData()
    {
        return $this->captures()[0] ?? NULL;
    }
    public function captures()
    {
        return $this->purchase_units[0]->payments->captures ?? [];
    }
    public function refunds()
    {
        return $this->purchase_units[0]->payments->refunds ?? [];
    }
    public function paymentVault()
    {
        $paymentSource = $this->paymentSource();
        if(is_null($paymentSource)) {
            return NULL;
        }
        return $paymentSource->attributes->vault ?? NULL;
    }
    public function paymentSource() : Entity\PaymentSourceResponse
    {
        if(!isset($this->payment_source)) {
            return NULL;
        }
        return Entity\PaymentSourceResponse::factory($this->payment_source);
    }
    public function packOrderResponse()
    {
        $orderResponseProperties = array_keys(get_class_vars("WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\OrderResponseTrait"));
        $vars = get_object_vars($this);
        $payload = (object) [];
        foreach ($orderResponseProperties as $property) {
            $payload->{$property} = is_object($this->{$property}) ? (object) [] : NULL;
            \WHMCS\Module\Gateway\paypal_ppcpv\Util::deepCopy($payload->{$property}, $this->{$property});
        }
        unset($payload->links);
        foreach ($payload->purchase_units ?? [] as $unit) {
            foreach ($unit->payments->captures ?? [] as $pay) {
                unset($pay->links);
            }
        }
        foreach ($payload->payment_source ?? [] as $pay) {
            unset($pay->attributes);
        }
        return json_encode($payload);
    }
    public function unpackOrderResponse(string $packed)
    {
        $decoded = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($packed);
        if($decoded === false) {
            return $this;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($decoded, $this);
    }
    public function transactionIdentifier()
    {
        return $this->captureData()->id ?? "";
    }
    public function link(string $relation)
    {
        if(!is_null($this->links)) {
            foreach ($this->links as $link) {
                if($link->rel === $relation) {
                    return $link;
                }
            }
        }
        return NULL;
    }
}

?>