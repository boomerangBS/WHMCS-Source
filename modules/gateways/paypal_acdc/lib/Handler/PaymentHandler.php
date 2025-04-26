<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc\Handler;

class PaymentHandler extends AbstractHandler
{
    public function createOrder(\WHMCS\Module\Gateway\paypal_acdc\API\Entity\AbstractPaymentSource $paymentSource, $cartCalculatorModel = NULL, $invoice) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateOrderResponse
    {
        return $this->asExtension("PaymentHandler")->createOrder($paymentSource, $cartCalculatorModel, $invoice);
    }
    public function invoiceOnApprove($invoiceId, string $orderId) : array
    {
        try {
            $orderStatusResponse = $this->as("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\OrderHandler")->orderStatus($orderId);
            if(!$orderStatusResponse->isLiabilityShifted()) {
                $this->log->orderDeclineLiability($orderStatusResponse);
                return [(new \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult())->setNotSuccessful()->setStatus("declined"), NULL];
            }
        } catch (\Exception $e) {
            return [(new \WHMCS\Module\Gateway\paypal_acdc\ModuleFunctionResult\CaptureResult())->setNotSuccessful()->setReason($e->__toString()), NULL];
        }
        return $this->captureOrder($orderId, $invoiceId);
    }
    public function captureOrder($orderId, string $invoiceId) : array
    {
        return $this->asExtension("PaymentHandler")->invoiceOnApprove($invoiceId, $orderId);
    }
    public function createSetupToken(\WHMCS\Module\Gateway\paypal_acdc\API\Entity\AbstractPaymentSource $paymentSource) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CreateSetupTokenResponse
    {
        return $this->asExtension("PaymentHandler")->createSetupToken($paymentSource);
    }
    public function createPaymentToken(\WHMCS\Module\Gateway\paypal_acdc\API\Entity\SetupTokenPaymentSource $paymentSource) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CreatePaymentTokenResponse
    {
        return $this->asExtension("PaymentHandler")->createPaymentToken($paymentSource);
    }
    public function capturePayment($orderId = NULL, $paymentSource) : \WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentResponse
    {
        return $this->asExtension("PaymentHandler")->capturePayment($orderId, $paymentSource);
    }
}

?>