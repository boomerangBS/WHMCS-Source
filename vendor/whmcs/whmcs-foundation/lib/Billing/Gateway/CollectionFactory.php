<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Gateway;

class CollectionFactory
{
    private $settings;
    private $systemIdentifiers;
    public function withSettings(\Illuminate\Support\Collection $settings) : \self
    {
        $this->settings = $settings;
        return $this;
    }
    public function withSystemIdentifiers($identifiers) : \self
    {
        $this->systemIdentifiers = $identifiers;
        return $this;
    }
    public function factory() : Collection
    {
        $identifiers = $this->systemIdentifiers ?? [];
        $settings = $this->settings ?? new \Illuminate\Support\Collection([]);
        $allPotentialPaymentGateways = self::factoryFromSystemIdentifiers($identifiers, $settings);
        $unknownModules = $settings->except($allPotentialPaymentGateways->keys());
        $unknownModules->each(function (\Illuminate\Support\Collection $storedSettings, $identifier) use($allPotentialPaymentGateways) {
            $missingModule = (new PaymentGatewayFactory($identifier))->withSettings($storedSettings)->factory();
            $allPotentialPaymentGateways->put($identifier, $missingModule);
        });
        return $allPotentialPaymentGateways;
    }
    public static function factoryFromInstalled() : Collection
    {
        return self::factoryFromSystemIdentifiers(PaymentGatewayServiceProvider::installedGatewayModuleIdentifiers());
    }
    public static function factoryFromSystemIdentifiers($identifiers = NULL, $settings) : Collection
    {
        $gatewayIdentifiers = collect($identifiers)->combine($identifiers);
        if(is_null($settings)) {
            $settings = PaymentGatewayServiceProvider::collectAllPaymentGatewaySettings();
        }
        $collection = $gatewayIdentifiers->map(function ($basename) use($settings) {
            $storedSettings = $settings->get($basename) ?? collect([]);
            return (new PaymentGatewayFactory($basename))->withSettings($storedSettings)->factory();
        });
        return new Collection($collection->filter());
    }
}

?>