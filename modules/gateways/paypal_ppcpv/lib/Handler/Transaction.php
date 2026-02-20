<?php

namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class Transaction extends AbstractHandler
{
    public function getTransaction($transactionIdentifier) : \WHMCS\Billing\Payment\Transaction\Information
    {
        $detail = (new \WHMCS\Billing\Payment\Transaction\Information())->setTransactionId($transactionIdentifier);
        $api = $this->api();
        $capture = function () use($api, $transactionIdentifier) {
            return [(new \WHMCS\Module\Gateway\paypal_ppcpv\API\PaymentCaptureLookupRequest($api))->setTransactionIdentifier($transactionIdentifier), "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentCaptureLookupResponse", function (\WHMCS\Billing\Payment\Transaction\Information &$detail, \WHMCS\Module\Gateway\paypal_ppcpv\API\PaymentCaptureLookupResponse $response) use($api) {
                return $this->decorateAsCapture($api, $detail, $response);
            }];
        };
        $refund = function () use($api, $transactionIdentifier) {
            return [(new \WHMCS\Module\Gateway\paypal_ppcpv\API\PaymentRefundLookupRequest($api))->setTransactionIdentifier($transactionIdentifier), "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentRefundLookupResponse", function (\WHMCS\Billing\Payment\Transaction\Information &$detail, \WHMCS\Module\Gateway\paypal_ppcpv\API\PaymentRefundLookupResponse $response) {
                return $this->decorateAsRefund($detail, $response);
            }];
        };
        foreach ([$capture, $refund] as $handler) {
            list($request, $expectedResponseType, $decorator) = $handler();
            $response = $api->send($request);
            if($response instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\RESTErrorResourceNotFound) {
            } elseif($request->isResponseType($response)) {
                $this->decorateCommon($detail, $response);
                $decorator($detail, $response);
                return $detail;
            }
        }
        throw new \WHMCS\Exception\Module\NotServicable(\AdminLang::trans("transactions.notfound"));
    }
    protected function decorateCommon(\WHMCS\Billing\Payment\Transaction\Information $detail, $response) : void
    {
        $detail->setTransactionId($response->id)->setCreated(\WHMCS\Carbon::parse($response->created_time))->setStatus($response->status);
        $detail->setAdditionalDatum("extendedStatus", $response->getStatusReason());
        $detail->setAdditionalDatum("disputed", \AdminLang::trans($response->getStatusReason() == "BUYER_COMPLAINT" ? "global.yes" : "global.no"));
        $detail->setDescription($response->note_to_payer);
    }
    protected function decorateAsCapture(\WHMCS\Module\Gateway\paypal_ppcpv\API\Controller $api, &$detail, $response) : void
    {
        $valueCurrency = function ($o) {
            return [$o->value ?? 0, (new \WHMCS\Billing\CurrencyData())->setCode(strtoupper($o->currency_code ?? ""))];
        };
        $detail->setType("capture")->setAmount(...$valueCurrency($response->amount ?? (object) []))->setMerchantAmount(...$valueCurrency($response->getMerchantNetAmount()))->setFee($response->getFeesTotal(), (new \WHMCS\Billing\CurrencyData())->setCode(strtoupper($response->seller_receivable_breakdown->paypal_fee->currency_code ?? "")))->setAdditionalDatum("disbursementMode", $response->disbursement_mode ?? "")->setAdditionalDatum("processorNetworkReference", $response->network_transaction_reference);
        if($response->hasOrderIdentifier()) {
            $orderResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusRequest($api))->setOrderIdentifier($response->getOrderIdentifier()));
            if($orderResponse instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusResponse) {
                $this->decorateOrderDetails($detail, $orderResponse);
            }
        }
    }
    protected function decorateAsRefund(\WHMCS\Billing\Payment\Transaction\Information $detail, $response) : void
    {
        $valueCurrency = function ($o) {
            return [($o->value ?? 0) * -1, (new \WHMCS\Billing\CurrencyData())->setCode(strtoupper($o->currency_code ?? ""))];
        };
        $detail->setType("refund")->addRelatedTransaction($response->getCaptureTransactionIdentifier(), "capture")->setAmount(...$valueCurrency($response->amount ?? (object) []))->setMerchantAmount(...$valueCurrency($response->getMerchantNetAmount()))->setFee($response->getFeesTotal(), (new \WHMCS\Billing\CurrencyData())->setCode(strtoupper($response->seller_payable_breakdown->paypal_fee->currency_code ?? "")))->setAdditionalDatum("gatewayNetworkReference", $response->acquirer_reference_number);
    }
    protected function decorateOrderDetails(\WHMCS\Billing\Payment\Transaction\Information $detail, $response) : void
    {
        $detail->setAdditionalDatum("gatewayOrder", $response->id);
        foreach ($response->refunds() as $refundTransaction) {
            if(isset($refundTransaction->id) && $refundTransaction->id != "") {
                $detail->addRelatedTransaction($refundTransaction->id, "refund");
            }
        }
        try {
            $paymentSource = $response->paymentSource();
            get_class($paymentSource);
            switch (get_class($paymentSource)) {
                case "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\Entity\\CardPaymentSourceResponse":
                    $this->decorateCard($detail, $paymentSource);
                    break;
                case "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\Entity\\PaypalPaymentSourceResponse":
                    $this->decoratePayPal($detail, $paymentSource);
                    break;
            }
        } catch (\InvalidArgumentException $e) {
        }
    }
    protected function decorateCard(\WHMCS\Billing\Payment\Transaction\Information $detail, $response) : void
    {
        $detail->setAdditionalDatum("paymentInstrument", sprintf("%s %s*%s", $response->networkType(), $response->brand(), $response->hint()));
    }
    protected function decoratePayPal(\WHMCS\Billing\Payment\Transaction\Information $detail, $response) : void
    {
        $payer = $response->getPayer();
        $detail->setAdditionalDatum("paymentInstrument", sprintf("PayPal: %s (%s)", $payer->fullName(), $payer->emailAddress));
    }
}

?>