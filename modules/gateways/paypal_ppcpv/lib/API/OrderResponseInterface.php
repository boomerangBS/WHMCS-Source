<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
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