<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module;

class Addon extends AbstractModule
{
    protected $type = self::TYPE_ADDON;
    const MODULE_NAME_PROJECT_MANAGEMENT = "project_management";
    public function getActiveModules()
    {
        return \WHMCS\Database\Capsule::table("tbladdonmodules")->distinct("module")->pluck("module")->all();
    }
    public function call($function, array $params = [])
    {
        $return = parent::call($function, $params);
        if(isset($return["jsonResponse"])) {
            $response = new \WHMCS\Http\JsonResponse();
            $response->setData($return["jsonResponse"]);
            $response->send();
            \WHMCS\Terminus::getInstance()->doExit();
        }
        return $return;
    }
    public function getAdminActivationForms($moduleName)
    {
        return [(new \WHMCS\View\Form())->setUriPrefixAdminBaseUrl("configaddonmods.php")->setMethod(\WHMCS\View\Form::METHOD_POST)->setParameters(["token" => generate_token("plain"), "action" => "activate", "module" => $moduleName])->setSubmitLabel(\AdminLang::trans("global.activate"))];
    }
    public function getAdminManagementForms($moduleName)
    {
        return [(new \WHMCS\View\Form())->setUriPrefixAdminBaseUrl("addonmodules.php")->setMethod(\WHMCS\View\Form::METHOD_GET)->setParameters(["module" => $moduleName])->setSubmitLabel(\AdminLang::trans("apps.info.useApp"))];
    }
    public function activate($parameters = [])
    {
        if(!$this->loadedmodule) {
            throw new \WHMCS\Exception("No module loaded");
        }
        if(!$this->functionExists("activate")) {
            throw new \WHMCS\Exception\Module\NotImplemented("Module cannot be activated due to no activate method being present");
        }
        if(!$this->functionExists("config")) {
            throw new \WHMCS\Exception\Module\NotImplemented("Module cannot be activated due to no config method being present");
        }
        $config = $this->call("config");
        if(empty($config) || !is_array($config)) {
            throw new \WHMCS\Exception\Module\NotActivated("Could not activate " . $this->getLoadedModule() . " due to invalid return from config method");
        }
        $activeModules = $this->getActiveModules();
        $response = $this->call("activate");
        if(!$response || is_array($response) && ($response["status"] === "success" || $response["status"] === "info")) {
            $version = $config["version"] ?: "1.0";
            $activeModules[] = $this->getLoadedModule();
            sort($activeModules);
            \WHMCS\Config\Setting::setValue("ActiveAddonModules", implode(",", $activeModules));
            if($version != \AdminLang::trans("addonmodules.nooutput")) {
                $addonModuleVersion = new Addon\Setting();
                $addonModuleVersion->module = $this->getLoadedModule();
                $addonModuleVersion->setting = "version";
                $addonModuleVersion->value = $version;
                $addonModuleVersion->save();
            }
            logActivity("Addon Module Activated - " . $config["name"]);
        } else {
            throw new \WHMCS\Exception\Module\NotActivated($response["description"] ?: "An unknown error occurred");
        }
    }
    public function isEnabled()
    {
        return in_array($this->getLoadedModule(), $this->getActiveModules());
    }
    public static function isModuleEnabled($moduleName)
    {
        $interface = new static();
        if($interface->load($moduleName) === false) {
            return false;
        }
        return $interface->isEnabled();
    }
    public static function getAvailableModules() : array
    {
        $modules = [];
        $addonDirectory = new \WHMCS\File\Directory("modules/addons");
        foreach ($addonDirectory->getSubdirectories() as $module) {
            if(file_exists(ROOTDIR . "/modules/addons/" . $module . "/" . $module . ".php")) {
                $modules[] = $module;
            }
        }
        try {
            $legacyDirectory = new \WHMCS\File\Directory("modules/admin");
            foreach ($legacyDirectory->getSubdirectories() as $module) {
                $modules[] = $module;
            }
        } catch (\Exception $e) {
        }
        return $modules;
    }
    public function getConfiguration()
    {
        $module = $this->getLoadedModule();
        if($module == "") {
            return NULL;
        }
        if(file_exists(ROOTDIR . "/modules/addons/" . $module . "/" . $module . ".php") && !function_exists($module . "_config")) {
            require_once ROOTDIR . "/modules/addons/" . $module . "/" . $module . ".php";
        }
        if(function_exists($module . "_config")) {
            return call_user_func($module . "_config");
        }
        if(file_exists(ROOTDIR . "/modules/admin") && file_exists(ROOTDIR . "/modules/admin/" . $module)) {
            return ["name" => titleCase(str_replace("_", " ", $module)), "version" => \AdminLang::trans("addonmodules.legacy"), "author" => "-"];
        }
        return NULL;
    }
    public static function getConfigurableModules() : array
    {
        $addonModules = [];
        foreach (static::getAvailableModules() as $availableModule) {
            $addonModule = new self();
            if(!$addonModule->load($availableModule)) {
            } else {
                $configuration = $addonModule->getConfiguration();
                if(!is_null($configuration)) {
                    $addonModules[$availableModule] = $configuration;
                }
            }
        }
        return $addonModules;
    }
}

?>