<?php

namespace WHMCS\Payment;

class SubscriptionObsolescence
{
    protected $paymentGateways;
    protected $supersedingPayMethods;
    protected $client;
    protected $subscriptionEntities;
    protected $obsoleteIdentifiers;
    protected $supersedingIdentifiers;
    public function __construct(\WHMCS\User\Client $client, array $obsoleteIdentifiers, array $supersedingIdentifiers, \WHMCS\Billing\Gateway\Collection $paymentGateways)
    {
        $this->client = $client;
        $this->obsoleteIdentifiers = $obsoleteIdentifiers;
        $this->supersedingIdentifiers = $supersedingIdentifiers;
        $this->paymentGateways = $paymentGateways;
    }
    public static function factory(\WHMCS\User\Client $client, array $obsoleteIdentifiers, array $supersedingIdentifiers)
    {
        $client->loadMissing(["services", "domains", "addons"]);
        $paymentGateways = \DI::make("WHMCS\\Billing\\Gateway\\PaymentGatewayServiceProvider")->all()->available();
        return new static($client, $obsoleteIdentifiers, $supersedingIdentifiers, $paymentGateways);
    }
    public function manage()
    {
        if(!$this->hasRequiredGatewaysAvailable() || !$this->isClientReady()) {
            return NULL;
        }
        $this->cancelSubscriptions();
        $this->updatePaymentGatewayForSubscribedEntities();
        $this->updateUnpaidInvoices();
        $this->updateClientsDefaultPaymentGateway();
    }
    protected function hasRequiredGatewaysAvailable()
    {
        $obsolete = $this->paymentGateways->only($this->obsoletePaymentGatewayIdentifiers());
        $superseding = $this->paymentGateways->only($this->supersedingPaymentGatewayIdentifiers());
        if(0 < count($obsolete) && 0 < count($superseding)) {
            return true;
        }
        return false;
    }
    public function isClientReady()
    {
        return $this->clientHasSupersedingPayMethods() && $this->clientHasObsoleteSubscriptions();
    }
    protected function client() : \WHMCS\User\Client
    {
        return $this->client;
    }
    protected function obsoletePaymentGatewayIdentifiers() : array
    {
        return $this->obsoleteIdentifiers;
    }
    protected function supersedingPaymentGatewayIdentifiers() : array
    {
        return $this->supersedingIdentifiers;
    }
    protected function supersedingPayMethods() : \Illuminate\Support\Collection
    {
        if(is_null($this->supersedingPayMethods)) {
            $payMethods = $this->client()->payMethods;
            $this->supersedingPayMethods = new \Illuminate\Support\Collection([]);
            foreach ($this->supersedingPaymentGatewayIdentifiers() as $identifier) {
                if($this->paymentGateways->has($identifier)) {
                    $methodsOfGateway = $payMethods->forGateway($identifier);
                    $this->supersedingPayMethods = $this->supersedingPayMethods->concat($methodsOfGateway);
                }
            }
        }
        return $this->supersedingPayMethods;
    }
    protected function clientHasSupersedingPayMethods()
    {
        return 0 < count($this->supersedingPayMethods());
    }
    protected function obsoleteSubscriptionServices() : ClientSubscriptionServices
    {
        if(is_null($this->subscriptionEntities)) {
            $this->subscriptionEntities = $this->client()->subscriptions($this->obsoletePaymentGatewayIdentifiers());
        }
        return $this->subscriptionEntities;
    }
    protected function clientHasObsoleteSubscriptions()
    {
        return 0 < count($this->obsoleteSubscriptionServices()->all());
    }
    protected function cancelSubscriptions()
    {
        $this->obsoleteSubscriptionServices()->cancelAllSubscriptions();
    }
    protected function updatePaymentGatewayForSubscribedEntities()
    {
        $cancelled = $this->obsoleteSubscriptionServices()->cancelled();
        $payMethod = $this->supersedingPayMethods()->first();
        $cancelled->each(function (\Illuminate\Support\Collection $entities) use($payMethod) {
            $entities->each(function (\WHMCS\Model\AbstractModel $model) use($payMethod) {
                $model->paymentmethod = $payMethod->gateway_name;
                $model->save();
            });
        });
    }
    protected function updateUnpaidInvoices()
    {
        $payMethod = $this->supersedingPayMethods()->first();
        $this->client()->invoices()->unpaid()->whereIn("paymentmethod", $this->obsoletePaymentGatewayIdentifiers())->get()->each(function (\WHMCS\Billing\Invoice $invoice) use($payMethod) {
            $invoice->paymentmethod = $payMethod->gateway_name;
            $invoice->paymethodid = $payMethod->id;
            $invoice->save();
        });
    }
    protected function updateClientsDefaultPaymentGateway()
    {
        $client = $this->client();
        $current = $client->defaultPaymentGateway ?? "";
        if(in_array($current, $this->obsoletePaymentGatewayIdentifiers())) {
            $payMethod = $this->supersedingPayMethods()->first();
            $client->defaultPaymentGateway = $payMethod->gateway_name;
            $client->save();
        }
    }
}

?>