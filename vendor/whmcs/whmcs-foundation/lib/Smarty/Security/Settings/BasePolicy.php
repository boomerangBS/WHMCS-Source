<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Smarty\Security\Settings;

class BasePolicy
{
    protected $phpFunctions;
    protected $phpModifiers;
    protected $allowedModifiers;
    protected $disabledModifiers;
    protected $allowedTags;
    protected $disabledTags;
    protected $staticClasses;
    protected $trustedStaticMethods;
    protected $trustedStaticProperties;
    protected $disabledSpecialSmartyVars;
    protected $enabledSpecialSmartyVars = [];
    protected $streams;
    protected $allowSuperGlobals;
    protected $allowConstants;
    protected $secureDir;
    protected $trustedDir;
    public function __construct(array $data)
    {
        $defaults = $this->getDefaultPolicySettings();
        foreach ($defaults as $key => $value) {
            if(array_key_exists($key, $data)) {
                $value = $data[$key];
            }
            $method = "set" . (string) \Illuminate\Support\Str::of($key)->studly();
            $this->{$method}($value);
        }
    }
    protected function getDefaultEnabledSpecialSmartyVars() : array
    {
        return [];
    }
    protected function getDefaultPolicySettings()
    {
        return ["php_functions" => [], "php_modifiers" => [], "allowed_modifiers" => [], "disabled_modifiers" => [], "allowed_tags" => [], "disabled_tags" => ["private_php"], "static_classes" => [], "trusted_static_methods" => [], "trusted_static_properties" => [], "disabled_special_smarty_vars" => ["foreach", "section", "block", "capture", "now", "cookies", "get", "post", "env", "server", "session", "request", "template", "template_object", "current_dir", "version", "const", "config", "ldelim", "rdelim"], "enabled_special_smarty_vars" => $this->getDefaultEnabledSpecialSmartyVars(), "streams" => [], "allow_super_globals" => true, "allow_constants" => true, "secure_dir" => [ROOTDIR], "trusted_dir" => []];
    }
    public function getPhpFunctions()
    {
        return $this->phpFunctions;
    }
    public function setphpFunctions($phpFunctions)
    {
        $this->phpFunctions = $phpFunctions;
        return $this;
    }
    public function getPhpModifiers()
    {
        return $this->phpModifiers;
    }
    public function setPhpModifiers($phpModifiers)
    {
        $this->phpModifiers = $phpModifiers;
        return $this;
    }
    public function getAllowedModifiers()
    {
        return $this->allowedModifiers;
    }
    public function setAllowedModifiers($allowedModifiers)
    {
        $this->allowedModifiers = $allowedModifiers;
        return $this;
    }
    public function getDisabledModifiers()
    {
        return $this->disabledModifiers;
    }
    public function setDisabledModifiers($disabledModifiers)
    {
        $this->disabledModifiers = $disabledModifiers;
        return $this;
    }
    public function getAllowedTags()
    {
        return $this->allowedTags;
    }
    public function setAllowedTags($allowedTags)
    {
        $this->allowedTags = $allowedTags;
        return $this;
    }
    public function getDisabledTags()
    {
        return $this->disabledTags;
    }
    public function setDisabledTags($disabledTags)
    {
        $this->disabledTags = $disabledTags;
        return $this;
    }
    public function getStaticClasses()
    {
        return $this->staticClasses;
    }
    public function setStaticClasses($staticClasses)
    {
        $this->staticClasses = $staticClasses;
        return $this;
    }
    public function getTrustedStaticMethods()
    {
        return $this->trustedStaticMethods;
    }
    public function setTrustedStaticMethods($staticMethods)
    {
        $this->trustedStaticMethods = $staticMethods;
        return $this;
    }
    public function getTrustedStaticProperties()
    {
        return $this->trustedStaticProperties;
    }
    public function setTrustedStaticProperties($trustedStaticProperties)
    {
        $this->trustedStaticProperties = $trustedStaticProperties;
        return $this;
    }
    public function getDisabledSpecialSmartyVars()
    {
        return $this->disabledSpecialSmartyVars;
    }
    public function setDisabledSpecialSmartyVars($disabledSpecialSmartyVars)
    {
        $this->disabledSpecialSmartyVars = $disabledSpecialSmartyVars;
        return $this;
    }
    public function getStreams()
    {
        return $this->streams;
    }
    public function setStreams($streams)
    {
        $this->streams = $streams;
        return $this;
    }
    public function isAllowSuperGlobals()
    {
        return $this->allowSuperGlobals;
    }
    public function setAllowSuperGlobals($allowSuperGlobals)
    {
        $this->allowSuperGlobals = $allowSuperGlobals;
        return $this;
    }
    public function getSecureDir()
    {
        return $this->secureDir;
    }
    public function setSecureDir($secureDir)
    {
        $this->secureDir = $secureDir;
        return $this;
    }
    public function getTrustedDir()
    {
        return $this->trustedDir;
    }
    public function setTrustedDir($trustedDir)
    {
        $this->trustedDir = $trustedDir;
        return $this;
    }
    public function isAllowConstants()
    {
        return $this->allowConstants;
    }
    public function setAllowConstants($allowConstants)
    {
        $this->allowConstants = $allowConstants;
        return $this;
    }
    public function hasPhpTagCompiler()
    {
        return !in_array(\WHMCS\Smarty\Security\Policy::TAG_COMPILER_PHP, (array) $this->getDisabledTags());
    }
    public function getEnabledSpecialSmartyVars() : array
    {
        return $this->enabledSpecialSmartyVars;
    }
    public function setEnabledSpecialSmartyVars($data)
    {
        $this->enabledSpecialSmartyVars = $data;
        return $this;
    }
}

?>