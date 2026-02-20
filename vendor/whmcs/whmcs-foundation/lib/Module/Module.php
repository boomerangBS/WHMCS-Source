<?php

namespace WHMCS\Module;

class Module
{
    protected $classMap = ["addons" => "Addon", "fraud" => "Fraud", "gateways" => "Gateway", "notifications" => "Notification", "registrars" => "Registrar", "security" => "Security", "servers" => "Server"];
    private static $hookLoads;
    protected $cacheActiveModules;
    public function getClassMap()
    {
        return $this->classMap;
    }
    public function getModules(array $moduleTypes = [])
    {
        $availableModuleTypes = array_keys($this->classMap);
        if(0 < count($moduleTypes)) {
            $availableModuleTypes = array_values(array_intersect($availableModuleTypes, $moduleTypes));
        }
        $moduleMap = [];
        foreach ($availableModuleTypes as $moduleType) {
            $moduleMap[$moduleType] = $this->getClassByModuleType($moduleType);
        }
        return $moduleMap;
    }
    public function getClassByModuleType($type)
    {
        if(!array_key_exists($type, $this->classMap)) {
            throw new \WHMCS\Exception("Invalid module type requested: " . $type);
        }
        $className = "\\WHMCS\\Module\\" . $this->classMap[$type];
        return new $className();
    }
    public static function sluggify($moduleType, $moduleName)
    {
        return strtolower(implode(".", [$moduleType, $moduleName]));
    }
    public static function defineHooks()
    {
        foreach (self::$hookLoads as $moduleType => $settingName) {
            $moduleHooks = array_filter(explode(",", \WHMCS\Config\Setting::getValue($settingName) ?? ""));
            foreach ($moduleHooks as $moduleHook) {
                $moduleHook = ROOTDIR . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $moduleType . DIRECTORY_SEPARATOR . $moduleHook . DIRECTORY_SEPARATOR . "hooks.php";
                if(file_exists($moduleHook)) {
                    \Hook::log("", "Attempting to load hook file: %s", $moduleHook);
                    $hookLoaded = \WHMCS\Utility\SafeInclude::file($moduleHook, function ($errorMessage) {
                        \Hook::log("", $errorMessage);
                    });
                    if($hookLoaded) {
                        \Hook::log("", "Hook File Loaded: %s", $moduleHook);
                    }
                }
            }
        }
    }
    public function getModuleType(AbstractModule $module)
    {
        $classMap = $this->getModules();
        return (string) array_search(get_class($module), $classMap);
    }
}

?>