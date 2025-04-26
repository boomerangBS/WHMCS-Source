<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class OrderHandler extends AbstractHandler
{
    public function orderStatus($orderId) : \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusResponse
    {
        $api = $this->api();
        $orderStatus = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusRequest($api))->setOrderIdentifier($orderId));
        if(!$orderStatus instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusResponse) {
            throw new \Exception($orderStatus->__toString());
        }
        return $orderStatus;
    }
    public function updateOrder($orderId, int $invoiceId, string $amountValue, string $amountCurrencyCode) : \WHMCS\Module\Gateway\paypal_ppcpv\API\UpdateOrderResponse
    {
        $api = $this->api();
        $updateRequest = (new \WHMCS\Module\Gateway\paypal_ppcpv\API\UpdateOrderRequest($api))->setOrderIdentifier($orderId)->updateInvoiceId($invoiceId)->updateAmount($amountCurrencyCode, $amountValue);
        $updateResponse = $api->send($updateRequest);
        if(!$updateResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\UpdateOrderResponse) {
            throw new \Exception($updateResponse->__toString());
        }
        return $updateResponse;
    }
}

?>