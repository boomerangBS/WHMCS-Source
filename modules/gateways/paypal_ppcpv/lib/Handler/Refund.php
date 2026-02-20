<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class Refund extends AbstractHandler
{
    public function handle($invoiceId, string $transactionId, string $amountValue, string $currencyCode) : array
    {
        $api = $this->api();
        $refundPaymentResponse = $this->postRefundPayment($api, $invoiceId, $transactionId, $amountValue, $currencyCode);
        if(!$refundPaymentResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\RefundPaymentResponse) {
            return $this->createErrorArray($transactionId, $refundPaymentResponse->__toString());
        }
        $refundDetailsResponse = $this->getRefundDetails($api, $refundPaymentResponse->getRefundIdentifier());
        if(!$refundDetailsResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\RefundDetailsResponse) {
            return $this->createErrorArray($transactionId, $refundDetailsResponse->__toString());
        }
        return $this->createSuccessArray($transactionId, $refundPaymentResponse->getRefundIdentifier(), $refundDetailsResponse->getPayPalFeeValue());
    }
    private function createSuccessArray(string $transactionId, string $refundId, string $payPalFeeValue)
    {
        return ["status" => "success", "rawdata" => ["action" => "refund", "transactionID" => $transactionId, "refundID" => $refundId], "transid" => $refundId, "fees" => $payPalFeeValue];
    }
    public function createErrorArray(string $transactionId, string $responseString)
    {
        return ["status" => "error", "rawdata" => ["action" => "refund", "transactionID" => $transactionId, "error" => $responseString]];
    }
    private function postRefundPayment(\WHMCS\Module\Gateway\paypal_ppcpv\API\Controller $api, string $invoiceId, string $transactionId, string $amountValue, string $currencyCode) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse
    {
        return $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\RefundPaymentRequest($api))->setTransactionId($transactionId)->setInvoiceId($invoiceId)->setAmount($amountValue, $currencyCode));
    }
    private function getRefundDetails(\WHMCS\Module\Gateway\paypal_ppcpv\API\Controller $api, string $refundIdentifier) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse
    {
        return $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\RefundDetailsRequest($api))->setRefundIdentifier($refundIdentifier));
    }
}

?>