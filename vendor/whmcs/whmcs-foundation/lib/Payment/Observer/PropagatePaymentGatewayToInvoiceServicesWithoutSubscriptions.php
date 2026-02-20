<?php

namespace WHMCS\Payment\Observer;

class PropagatePaymentGatewayToInvoiceServicesWithoutSubscriptions implements ObserverInterface
{
    public function observe(\WHMCS\Payment\Event\PaymentBySupersedingGateway $event) : void
    {
        $fetchRelationsWithGateway = function ($invoice) {
            return $invoice->items->map(function (\WHMCS\Billing\Invoice\Item $item) {
                $relation = $item->getRelatedEntity();
                if($relation instanceof \WHMCS\Service\PaymentGatewayAwareInterface) {
                    return $relation;
                }
                return NULL;
            })->filter();
        };
        $hasSubscription = function ($item) {
            if(!$item instanceof \WHMCS\Service\SubscriptionAwareInterface) {
                return false;
            }
            return (bool) $item->getSubscriptionId();
        };
        $isAlreadyAssigned = function ($relation, $paymentGateway) {
            return $relation->getPaymentGatewayIdentifier() === $paymentGateway->systemIdentifier();
        };
        $updatePaymentGateway = function ($relation, $paymentGateway) {
            $relation->addPaymentGatewayIdentifier($paymentGateway->systemIdentifier());
        };
        $paymentGateway = $event->paymentGateway();
        $gatewayRelations = $fetchRelationsWithGateway($event->invoice());
        foreach ($gatewayRelations as $paymentGatewayAwareRelation) {
            if($hasSubscription($paymentGatewayAwareRelation) || $isAlreadyAssigned($paymentGatewayAwareRelation, $paymentGateway)) {
            } else {
                $updatePaymentGateway($paymentGatewayAwareRelation, $paymentGateway);
                $paymentGatewayAwareRelation->save();
            }
        }
        return NULL;
    }
}

?>