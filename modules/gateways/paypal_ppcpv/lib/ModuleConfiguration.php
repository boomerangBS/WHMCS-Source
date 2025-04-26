<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv;

// Decoded file for php version 72.
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F4D6F64756C65436F6E66696775726174696F6E2E7068703078376664353934323439623666_
{
    public $identifier;
    public $label;
    public $phpType;
    public $sensitive = false;
    public $description = "";
    public $textWidth = 0;
    public $readOnly = false;
    public $defaultValue;
    public function exists($settings) : array
    {
        return isset($settings[$this->identifier]);
    }
    public function asValue(array $settings)
    {
        switch ($this->phpType) {
            case "bool":
                return WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::toggle($settings, $this->identifier);
                break;
            case "string":
                return WHMCS\Module\Gateway\paypal_ppcpv\ModuleConfiguration::text($settings, $this->identifier);
                break;
            default:
                throw new Exception("unknown type '" . $this->phpType . "'");
        }
    }
    public function toValue($value)
    {
        switch ($this->phpType) {
            case "bool":
                return $value ? "on" : "";
                break;
            case "string":
                return $value;
                break;
            default:
                throw new Exception("unknown type '" . $this->phpType . "'");
        }
    }
    public function default()
    {
        switch ($this->phpType) {
            case "bool":
                return false;
                break;
            case "string":
                return "";
                break;
        }
    }
    public function typedDefault(string $phpType, $value)
    {
        $c = clone $this;
        $c->phpType = $phpType;
        switch ($c->phpType) {
            case "bool":
                switch ($value) {
                    case "true":
                        return true;
                        break;
                    case "false":
                        return false;
                        break;
                }
                break;
            case "string":
                return (string) $value;
                break;
            default:
                return $c->default();
        }
    }
}
class ModuleConfiguration
{
    public $clientId;
    protected static $clientSecretSpec = ["label" => "Client Secret", "size" => 70, "readonly" => true, "sensitive" => true];
    public $clientSecret;
    protected static $payerIdSpec = ["label" => "Merchant Payer ID", "size" => 70, "readonly" => true];
    public $payerId;
    protected static $sandboxClientIdSpec = ["label" => "Client ID (Sandbox)", "size" => 70, "readonly" => true];
    public $sandboxClientId;
    protected static $sandboxClientSecretSpec = ["label" => "Client Secret (Sandbox)", "size" => 70, "readonly" => true, "sensitive" => true];
    public $sandboxClientSecret = ["name", "visible", "webhook-identifier", "merchant-status"];
    protected static $sandboxPayerIdSpec = ["label" => "Merchant Payer ID (Sandbox)", "size" => 70, "readonly" => true];
    public $sandboxPayerId = [];
    protected static $useSandboxSpec = ["label" => "Test Mode", "description" => "Check to use <a href=\"https://developer.paypal.com/\">PayPal's Sandbox Test Environment</a>.", "default" => true, "var" => "bool"];
    public $useSandbox;
    protected $fieldSpecifications;
    protected $systemSettings;
    protected $systemValues;
    protected static $clientIdSpec = ["label" => "Client ID", "size" => 70, "readonly" => true];
    public static function persistedSettings() : array
    {
        return \WHMCS\Module\GatewaySetting::getForGateway(PayPalCommerce::MODULE_NAME);
    }
    public static function fromPersistance() : \self
    {
        return (new static())->fromSettings(static::persistedSettings());
    }
    public function fromSettings($settings) : \self
    {
        foreach ($this->fields() as $spec) {
            $value = $spec->defaultValue;
            if($spec->exists($settings)) {
                $value = $spec->asValue($settings);
            }
            $this->{$spec->identifier} = $value;
            unset($settings[$spec->identifier]);
        }
        $field = $this->fieldVisible();
        $this->setVisible($field->asValue($settings));
        unset($settings[$field->identifier]);
        unset($field);
        $field = $this->fieldGatewayName();
        $this->setGatewayName($field->asValue($settings));
        unset($settings[$field->identifier]);
        unset($field);
        foreach ($settings as $setting => $value) {
            $this->systemValues[$setting] = $value;
        }
        return $this;
    }
    public function setGatewayName($v) : \self
    {
        $field = $this->fieldGatewayName();
        $this->systemValues[$field->identifier] = $v;
        return $this;
    }
    public function getGatewayName()
    {
        $field = $this->fieldGatewayName();
        return $this->systemValue($field->identifier) ?? $field->default();
    }
    public function getGatewayDefaultName()
    {
        return $this->fieldGatewayName()->defaultValue;
    }
    public function setVisible($v) : \self
    {
        $field = $this->fieldVisible();
        $this->systemValues[$field->identifier] = $v;
        return $this;
    }
    public function getVisible()
    {
        $field = $this->fieldVisible();
        return $this->systemValue($field->identifier) ?? $field->default();
    }
    public function getSignatureVerificationSetting()
    {
        if($this->systemValue("SignatureVerification") === "disable") {
            return false;
        }
        return true;
    }
    public function setWebhookIdentifier(Environment $env, string $v) : \self
    {
        $this->systemValues[$this->withEnvironmentSuffix($env, "webhook-identifier")] = $v;
        return $this;
    }
    public function getWebhookIdentifier(Environment $env) : Environment
    {
        return $this->systemValues[$this->withEnvironmentSuffix($env, "webhook-identifier")] ?? "";
    }
    public function setMerchantStatus(Environment $env, $mask) : \self
    {
        $key = $this->withEnvironmentSuffix($env, "merchant-status");
        $this->systemValues[$key] = $mask->mask();
        return $this;
    }
    public function getMerchantStatus(Environment $env) : MerchantStatusSetting
    {
        $key = $this->withEnvironmentSuffix($env, "merchant-status");
        return new MerchantStatusSetting((int) ($this->systemValues[$key] ?? MerchantStatusSetting::DEFAULT));
    }
    protected function withEnvironmentSuffix(Environment $env, string $setting) : Environment
    {
        switch ($env->label) {
            case Environment::LIVE:
                return $setting;
                break;
            case Environment::SANDBOX:
                return $setting . "-sandbox";
                break;
            default:
                throw new \Exception("Unknown environment " . $env->label);
        }
    }
    protected function systemValue(string $setting)
    {
        return $this->systemValues[$setting] ?? NULL;
    }
    protected function systemValues() : array
    {
        $systemValues = $this->systemValues;
        unset($systemValues["name"]);
        unset($systemValues["visible"]);
        return $systemValues;
    }
    public function withEnvironmentCredentials(Environment $e) : \self
    {
        switch ($e->label) {
            case Environment::LIVE:
                $this->clientId = $e->clientId;
                $this->clientSecret = $e->clientSecret;
                $this->payerId = $e->payerId;
                break;
            case Environment::SANDBOX:
                $this->sandboxClientId = $e->clientId;
                $this->sandboxPayerId = $e->payerId;
                $this->sandboxClientSecret = $e->clientSecret;
                return $this;
                break;
            default:
                throw new \Exception("Unknown environment " . $e->label);
        }
    }
    public function unlink(Environment $env) : \self
    {
        $env = clone $env;
        $this->setWebhookIdentifier($env, "");
        $this->setMerchantStatus($env, MerchantStatusSetting::make());
        $this->withEnvironmentCredentials($env->unlink());
        return $this;
    }
    public function persist(\WHMCS\Module\Gateway $module)
    {
        $class = new \ReflectionClass($this);
        $configurationFields = [];
        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $p) {
            $field = static::fieldSpecification($p);
            $configurationFields[$p->getName()] = $field->toValue($p->getValue($this));
        }
        $module->updateConfiguration($configurationFields);
        foreach ($this->systemValues() as $setting => $value) {
            $module->saveConfigValue($setting, $value);
        }
        return $this;
    }
    protected function fields() : array
    {
        if(isset($this->fieldSpecifications)) {
            return $this->fieldSpecifications;
        }
        $this->fieldSpecifications = static::fieldSpecifications();
        return $this->fieldSpecifications;
    }
    protected static function fieldSpecifications() : array
    {
        $fields = [];
        $class = new \ReflectionClass("WHMCS\\Module\\Gateway\\paypal_ppcpv\\ModuleConfiguration");
        foreach ($class->getProperties() as $property) {
            if(!$property->isPublic()) {
            } else {
                $spec = static::fieldSpecification($property);
                $fields[$spec->identifier] = $spec;
            }
        }
        return $fields;
    }
    protected function fieldVisible()
    {
        $f = self::newField();
        $f->identifier = "visible";
        $f->phpType = "bool";
        return $f;
    }
    protected function fieldGatewayName()
    {
        $f = self::newField();
        $f->identifier = "name";
        $f->phpType = "string";
        $f->defaultValue = PayPalCommerce::DISPLAY_NAME;
        return $f;
    }
    protected static function newField()
    {
        return new func_num_args();
    }
    protected static function fieldSpecification(\ReflectionProperty $property)
    {
        $field = static::newField();
        $specProperty = $property->getDeclaringClass()->getProperty(sprintf("%sSpec", $property->getName()));
        $specProperty->setAccessible(true);
        $spec = (object) $specProperty->getValue();
        $field->identifier = $property->getName();
        $field->phpType = $spec->var ?? "string";
        $field->label = $spec->label ?? "";
        $field->description = $spec->description ?? "";
        $field->defaultValue = $field->typedDefault($field->phpType, $spec->default ?? NULL);
        $field->textWidth = $spec->size ?? 0;
        $field->readOnly = $spec->readonly ?? false;
        $field->sensitive = $spec->sensitive ?? false;
        return $field;
    }
    public function toConfig() : array
    {
        $config = ["FriendlyName" => ["Type" => "System", "Value" => $this->fieldGatewayName()->defaultValue]];
        foreach ($this->fields() as $spec) {
            $fieldConfig = [];
            $config[$spec->identifier] =& $fieldConfig;
            $fieldConfig["Type"] = self::fieldSpecificationToModuleFieldType($spec);
            $fieldConfig["FriendlyName"] = $spec->label;
            if(0 < strlen($spec->description)) {
                $fieldConfig["Description"] = $spec->description;
            }
            if(0 < $spec->textWidth) {
                $fieldConfig["Size"] = $spec->textWidth;
            }
            if($spec->readOnly) {
                $fieldConfig["ReadOnly"] = true;
            }
            $fieldConfig["Value"] = $spec->defaultValue;
            unset($fieldConfig);
        }
        return $config;
    }
    protected static function extractAttribute($at, string $docblock)
    {
        $atLength = strlen($at);
        $pos = strpos($docblock, $at);
        if($pos === false) {
            return NULL;
        }
        $hunk = ltrim(substr($docblock, $pos + $atLength));
        $pos = strpos($hunk, "*");
        if($pos === false) {
            $pos = 0;
        }
        return trim(substr($hunk, 0, max(0, $pos - 1)));
    }
    protected static function hasAttribute($at, string $docblock)
    {
        if(strpos($docblock, $at) === false) {
            return false;
        }
        return true;
    }
    protected static function fieldSpecificationToModuleFieldType($fieldSpec)
    {
        if($fieldSpec->phpType == "bool") {
            return "yesno";
        }
        if($fieldSpec->phpType == "string") {
            if($fieldSpec->sensitive) {
                return "password";
            }
            return "text";
        }
        throw new \Exception("Failed to resolve field-type for " . $fieldSpec->phpType);
    }
    public static function toggle($settings, string $setting) : array
    {
        return isset($settings[$setting]) && $settings[$setting] == "on";
    }
    public static function text($settings, string $setting) : array
    {
        if(isset($settings[$setting])) {
            return $settings[$setting];
        }
        return "";
    }
    public function activeEnvironment() : Environment
    {
        return Environment::factory($this);
    }
    public function resolveVisibleState()
    {
        return $this->activeEnvironment()->hasCredentials();
    }
}

?>