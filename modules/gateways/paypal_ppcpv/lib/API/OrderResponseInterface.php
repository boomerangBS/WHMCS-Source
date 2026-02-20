<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
interface OrderResponseInterface
{
    public function isCaptureComplete();
    public function isCapturePending();
    public function isCaptureDeclined();
    public function captureData();
    public function paymentVault();
    public function paymentSource() : Entity\PaymentSourceResponse;
    public function packOrderResponse();
    public function unpackOrderResponse(string $packed);
    public function link(string $relation);
}

?>