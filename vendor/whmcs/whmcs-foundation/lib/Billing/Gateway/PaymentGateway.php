<?php

namespace WHMCS\Billing\Gateway;

class PaymentGateway implements Contract\ModulePaymentGatewayInterface
{
    private $systemIdentifier;
    private $module;
    private $settings;
    public function __construct(string $systemIdentifier, \Illuminate\Support\Collection $settings, \WHMCS\Module\Gateway $module)
    {
        $this->systemIdentifier = $systemIdentifier;
        $this->settings = $settings;
        $this->module = $module;
    }
    public function getModule() : \WHMCS\Module\Gateway
    {
        return $this->module;
    }
    public function isServiceable()
    {
        return !$this->getModule() instanceof Contract\NotServiceableInterface;
    }
    public function isActive()
    {
        $value = $this->getSettingValue("name");
        if(is_null($value) || $value === "") {
            return false;
        }
        return true;
    }
    public function isAvailable()
    {
        return $this->isServiceable() && $this->isActive();
    }
    public function isSupportedCurrency(\WHMCS\Billing\Currency $currency) : \WHMCS\Billing\Currency
    {
        $module = $this->getModule();
        $supportedCurrencies = $module->getMetaDataValue("supportedCurrencies");
        if(!is_array($supportedCurrencies) || empty($supportedCurrencies) || in_array($currency->code, $supportedCurrencies)) {
            return true;
        }
        return false;
    }
    public function getSettings() : \Illuminate\Support\Collection
    {
        return $this->settings;
    }
    private function getSettingValue($settingName)
    {
        $setting = $this->getSettings()->get($settingName);
        if(is_null($setting)) {
            return NULL;
        }
        return $setting->value;
    }
    public function hasShowOnOrderForm()
    {
        $value = $this->getSettingValue("visible");
        if($value === "on") {
            return true;
        }
        return false;
    }
    public function sortOrderRank() : int
    {
        $nameSetting = $this->getSettings()->get("name");
        if(is_null($nameSetting)) {
            return -1;
        }
        return (int) $nameSetting->getRawAttribute("order");
    }
    public function systemIdentifier()
    {
        return $this->systemIdentifier;
    }
    public function displayName()
    {
        return $this->configuredDisplayName() ?? $this->canonicalDisplayName();
    }
    public function canonicalDisplayName()
    {
        $name = $this->metadataDisplayName() ?? $this->moduleManifestDisplayName();
        if(is_null($name)) {
            $name = $this->moduleConfigDisplayName() ?? $this->systemIdentifier();
        }
        return $name;
    }
    private function configuredDisplayName()
    {
        return $this->getSettingValue("name");
    }
    public function type()
    {
        $storedValue = $this->getSettingValue("type");
        if(!empty($storedValue)) {
            return $storedValue;
        }
        return $this->typeByDefinition();
    }
    public function typeByDefinition()
    {
        $validTypes = [\WHMCS\Module\Gateway::GATEWAY_BANK, \WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD, \WHMCS\Module\Gateway::GATEWAY_THIRD_PARTY];
        $module = $this->getModule();
        $metaValue = $module->getMetaDataValue("gatewayType");
        if(in_array($metaValue, $validTypes)) {
            return $metaValue;
        }
        if($module->functionExists("capture")) {
            return \WHMCS\Module\Gateway::GATEWAY_CREDIT_CARD;
        }
        return \WHMCS\Module\Gateway::GATEWAY_THIRD_PARTY;
    }
    private function metadataDisplayName()
    {
        $name = $this->getModule()->getMetaDataValue("DisplayName");
        if($name !== "") {
            return $name;
        }
        return NULL;
    }
    private function moduleManifestDisplayName()
    {
        return NULL;
    }
    private function moduleConfigDisplayName()
    {
        $configDefinition = $this->getModule()->getConfiguration();
        if(isset($configDefinition["FriendlyName"]) && isset($configDefinition["FriendlyName"]["Value"]) && $configDefinition["FriendlyName"]["Value"] !== "") {
            return $configDefinition["FriendlyName"]["Value"];
        }
        return NULL;
    }
    public function isObsolete()
    {
        return (bool) $this->getModule()->getMetaDataValue("obsolete");
    }
    public function getSupersedingSystemIdentifiers() : array
    {
        return (array) $this->getModule()->getMetaDataValue("supersededBy");
    }
    public function isSuperseded()
    {
        $supersededBy = $this->getSupersedingSystemIdentifiers();
        return 0 < count($supersededBy);
    }
    public function supersededBy() : Collection
    {
        $supersededBy = $this->getSupersedingSystemIdentifiers();
        $baseCollection = \DI::make("WHMCS\\Billing\\Gateway\\PaymentGatewayServiceProvider")->all();
        return $baseCollection->serviceable()->only($supersededBy);
    }
    public function isSubscriptionCapable()
    {
        return $this->getModule()->functionExists("cancelSubscription");
    }
    public function cancelSubscription($subscriptionId) : array
    {
        $defaultError = ["status" => "error", "errorMsg" => "", "rawdata" => "Unexpected module response"];
        if(!$this->isSubscriptionCapable()) {
            $defaultError["errorMsg"] = "Module does not support subscription management.";
            return $defaultError;
        }
        $cancelResult = $this->getModule()->call("cancelSubscription", ["subscriptionID" => $subscriptionId]);
        if(!is_array($cancelResult)) {
            $cancelResult = $defaultError;
        } else {
            foreach ($defaultError as $key => $value) {
                $cancelResult[$key] = $cancelResult[$key] ?? $value;
            }
        }
        return $cancelResult;
    }
}

?>