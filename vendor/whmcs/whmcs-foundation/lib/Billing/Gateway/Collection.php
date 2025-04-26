<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Gateway;

class Collection extends \Illuminate\Support\Collection
{
    public function reloadAll()
    {
        $new = \DI::make("WHMCS\\Billing\\Gateway\\PaymentGatewayServiceProvider")->all();
        $this->reloadWith($new);
    }
    public function reloadWith(\Illuminate\Support\Collection $new)
    {
        $this->forget($this->keys()->all());
        $this->loadWith($new);
    }
    private function loadWith(\Illuminate\Support\Collection $new)
    {
        $new->each(function ($value, $key) {
            $this->put($key, $value);
        });
    }
    public function reloadSystemIdentifiers(array $systemIdentifier)
    {
        $new = CollectionFactory::factoryFromSystemIdentifiers($systemIdentifier);
        $this->loadWith($new);
    }
    private function callItemMethod($method = [], array $arguments) : \Closure
    {
        return function (Contract\PaymentGatewayInterface $paymentGateway) use($method, $arguments) {
            return call_user_func_array([$paymentGateway, $method], $arguments);
        };
    }
    public function serviceable()
    {
        return $this->filter($this->callItemMethod("isServiceable"));
    }
    public function notServiceable() : \self
    {
        return $this->reject($this->callItemMethod("isServiceable"));
    }
    public function active() : \self
    {
        return $this->filter($this->callItemMethod("isActive"));
    }
    public function notActive() : \self
    {
        return $this->reject($this->callItemMethod("isActive"));
    }
    public function available() : \self
    {
        return $this->filter($this->callItemMethod("isAvailable"));
    }
    public function configuredForOrderForm() : \self
    {
        return $this->filter($this->callItemMethod("hasShowOnOrderForm"));
    }
    public function notConfiguredForOrderForm() : \self
    {
        return $this->reject($this->callItemMethod("hasShowOnOrderForm"));
    }
    public function obsolete() : \self
    {
        return $this->filter($this->callItemMethod("isObsolete"));
    }
    private function supersedingByObsoletedIdentifier() : \self
    {
        $obsolete = $this->obsolete();
        return $obsolete->map(function (Contract\PaymentGatewayInterface $obsoletePaymentGateway) {
            $supersedingPossibilities = $obsoletePaymentGateway->supersededBy();
            $supersedingViable = $this->only($supersedingPossibilities->keys());
            $firstViable = $supersedingViable->shift();
            if($firstViable) {
                return $firstViable;
            }
            return NULL;
        })->filter();
    }
    public function withoutSupersededObsolete() : \self
    {
        return $this->except($this->supersedingByObsoletedIdentifier()->serviceable()->keys());
    }
    public function supportsCurrency(\WHMCS\Billing\CurrencyInterface $currency) : \self
    {
        return $this->filter($this->callItemMethod("isSupportedCurrency", [$currency]));
    }
    public function sortByAdminConfiguration() : \self
    {
        $notRanked = [];
        $initialSort = $this->sortBy(function (Contract\PaymentGatewayInterface $paymentGateway, $key) use($notRanked) {
            $rank = $paymentGateway->sortOrderRank();
            if($rank < 1) {
                $notRanked[$key] = $paymentGateway;
            }
            return $rank;
        });
        $sorted = $initialSort->forget(array_keys($notRanked));
        foreach ($notRanked as $key => $value) {
            $sorted->put($key, $value);
        }
        return $sorted;
    }
    public function displayNameMap() : \Illuminate\Support\Collection
    {
        return $this->map($this->callItemMethod("displayName"));
    }
}

?>