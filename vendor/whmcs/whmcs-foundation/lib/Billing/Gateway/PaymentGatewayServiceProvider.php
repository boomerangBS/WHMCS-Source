<?php

namespace WHMCS\Billing\Gateway;

class PaymentGatewayServiceProvider extends \WHMCS\Application\Support\ServiceProvider\AbstractServiceProvider
{
    private $allPaymentGateways;
    public function register()
    {
        $app = $this->app;
        $app->singleton(static::class, function () {
            static $app = NULL;
            return new static($app);
        });
    }
    public function all() : Collection
    {
        if(is_null($this->allPaymentGateways) || count($this->allPaymentGateways) < 1) {
            $this->allPaymentGateways = (new CollectionFactory())->withSettings(static::collectAllPaymentGatewaySettings())->withSystemIdentifiers(static::installedGatewayModuleIdentifiers())->factory();
        }
        return $this->allPaymentGateways;
    }
    public static function collectAllPaymentGatewaySettings() : \Illuminate\Support\Collection
    {
        $storedSettings = \WHMCS\Module\GatewaySetting::all();
        $settingsByGateway = [];
        $storedSettings->each(function (\WHMCS\Module\GatewaySetting $gatewaySetting) use($settingsByGateway) {
            $moduleName = $gatewaySetting->gateway;
            if(!isset($settingsByGateway[$moduleName])) {
                $settingsByGateway[$moduleName] = collect([]);
            }
            $setting = $gatewaySetting->setting;
            $settingsByGateway[$moduleName]->put($setting, $gatewaySetting);
        });
        return collect($settingsByGateway);
    }
    public static function collectSettingsForIdentifier($systemIdentifier) : \Illuminate\Support\Collection
    {
        $storedSettings = \WHMCS\Module\GatewaySetting::gateway($systemIdentifier)->get();
        $settingNames = [];
        $storedSettings->each(function (\WHMCS\Module\GatewaySetting $gatewaySetting) use($settingNames) {
            $settingNames[] = $gatewaySetting->setting;
        });
        return collect($settingNames)->combine($storedSettings);
    }
    public static function installedGatewayModuleIdentifiers() : array
    {
        $gatewayModuleDir = "/modules/gateways";
        $files = collect((new \WHMCS\File\Directory($gatewayModuleDir))->listFiles())->filter(function (string $basename) {
            $isModuleFunctionFile = (bool) preg_match("/^[a-zA-Z][a-zA-Z0-9_-]*\\.php\$/", $basename);
            return $isModuleFunctionFile && $basename !== "index.php";
        })->map(function ($basename) {
            return substr($basename, 0, -4);
        })->unique()->sort()->toArray();
        return $files;
    }
}

?>