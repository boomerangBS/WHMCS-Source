<?php

class Plesk_ValueAddedServiceManager
{
    private $extensions;
    private $licenseData;
    private $params;
    const EXTENSION_WP_TOOLKIT = "wp-toolkit";
    const EXTENSION_SITEJET = "plesk-sitejet";
    const EXTENSIONS = ["wp-toolkit" => ["display_name" => "WP Toolkit", "license_prop" => "wordpress-toolkit"], "plesk-sitejet" => ["display_name" => "Sitejet Builder", "license_prop" => "sitebuilder"]];
    const VAS_WP_TOOLKIT_SMART = "wp-toolkit";
    const VAS_WP_TOOLKIT = "wp-toolkit-not-smart";
    const VAS_SITEJET_BUILDER = "sitejet";
    const VALUE_ADDED_SERVICES = ["wp-toolkit" => ["name" => "WordPress Toolkit with Smart Updates", "required_extensions" => ["wp-toolkit"], "limits" => ["ext_limit_wp_toolkit_wp_instances" => -1, "ext_limit_wp_toolkit_wp_backups" => -1, "ext_limit_wp_toolkit_smart_update_instances" => -1], "permissions" => ["ext_permission_wp_toolkit_manage_wordpress_toolkit" => true, "ext_permission_wp_toolkit_manage_security_wordpress_toolkit" => true, "ext_permission_wp_toolkit_manage_cloning" => true, "ext_permission_wp_toolkit_manage_syncing" => true, "ext_permission_wp_toolkit_manage_autoupdates" => true]], "wp-toolkit-not-smart" => ["name" => "WordPress Toolkit", "required_extensions" => ["wp-toolkit"], "limits" => ["ext_limit_wp_toolkit_wp_instances" => -1, "ext_limit_wp_toolkit_wp_backups" => -1, "ext_limit_wp_toolkit_smart_update_instances" => 0], "permissions" => ["ext_permission_wp_toolkit_manage_wordpress_toolkit" => true, "ext_permission_wp_toolkit_manage_security_wordpress_toolkit" => true, "ext_permission_wp_toolkit_manage_cloning" => true, "ext_permission_wp_toolkit_manage_syncing" => true, "ext_permission_wp_toolkit_manage_autoupdates" => true]], "sitejet" => ["name" => "Sitejet Builder", "required_extensions" => ["plesk-sitejet"], "permissions" => ["ext_permission_plesk_sitejet_create_sitejet_site" => true, "ext_permission_plesk_sitejet_access_xovi_integration" => true]]];
    public function __construct(array $params)
    {
        $this->params = $params;
    }
    public function getExtensionNames() : array
    {
        return array_keys(static::EXTENSIONS);
    }
    protected function updateAvailableExtensions() : void
    {
        if(!is_null($this->extensions)) {
            return NULL;
        }
        $extensionData = Plesk_Registry::getInstance()->manager->getExtensions($this->params);
        $availableExtensions = [];
        foreach ($extensionData->details as $extensionDataItem) {
            $extensionDataItem = (array) $extensionDataItem;
            $availableExtensions[$extensionDataItem["id"]] = $extensionDataItem;
        }
        $this->extensions = $availableExtensions;
    }
    protected function updateLicenseData() : void
    {
        if(!is_null($this->licenseData)) {
            return NULL;
        }
        $licenseKeyData = Plesk_Registry::getInstance()->manager->getLicenseKey($this->params);
        $licenseData = [];
        foreach ($licenseKeyData->key->property as $propData) {
            $propData = (array) $propData;
            $licenseData[$propData["name"]] = $propData["value"];
        }
        $this->licenseData = $licenseData;
    }
    protected function isExtensionInstalled($name)
    {
        $this->updateAvailableExtensions();
        return !empty($this->extensions[$name]);
    }
    protected function isExtensionActive($name)
    {
        $this->updateAvailableExtensions();
        if(!$this->isExtensionInstalled($name)) {
            return false;
        }
        return strtolower($this->extensions[$name]["active"] ?? "") === "true";
    }
    protected function isExtensionLicensed($name)
    {
        $extensionLicensePropName = ["wp-toolkit" => ["display_name" => "WP Toolkit", "license_prop" => "wordpress-toolkit"], "plesk-sitejet" => ["display_name" => "Sitejet Builder", "license_prop" => "sitebuilder"]][$name]["license_prop"] ?? NULL;
        if(is_null($extensionLicensePropName)) {
            throw new WHMCS\Exception\Module\NotServicable("Invalid/unknown Plesk extension name: " . $name);
        }
        $this->updateLicenseData();
        return !empty($this->licenseData[$extensionLicensePropName]);
    }
    public function canManageServiceAddonPlans()
    {
        $this->updateLicenseData();
        return !empty($this->licenseData["can-manage-accounts"]);
    }
    public function getValueAddedServicesList() : array
    {
        return $this->assertRequiredValueAddedServicesExist();
    }
    public function assertManageServiceAddonPlanCapable() : void
    {
        if(!$this->canManageServiceAddonPlans()) {
            throw new WHMCS\Exception\Module\NotServicable("Your Plesk license does not allow managing Value Added Services on a per account basis");
        }
    }
    public function assertRequiredValueAddedServicesExist() : array
    {
        $this->assertManageServiceAddonPlanCapable();
        $addons = $this->getServicePlanAddons();
        if(!$this->hasAllRequiredValueAddedServices($addons)) {
            try {
                $this->createValueAddedServices($addons);
            } catch (Exception $e) {
            }
            $addons = $this->getServicePlanAddons();
        }
        return $addons;
    }
    public function hasAllRequiredValueAddedServices($existingAddons) : array
    {
        $hasAllAddons = false;
        if(count($existingAddons) == 0) {
            return $hasAllAddons;
        }
        $hasAllAddons = true;
        foreach (static::VALUE_ADDED_SERVICES as $vasTemplate) {
            if(!in_array($vasTemplate["name"], $existingAddons)) {
                $hasAllAddons = false;
                return $hasAllAddons;
            }
        }
    }
    protected function getServicePlanAddons() : array
    {
        $instance = Plesk_Registry::getInstance();
        try {
            $xml = $instance->manager->getServicePlanAddons();
        } catch (Exception $e) {
            throw new WHMCS\Exception\Module\NotServicable(sprintf("GetExtensions failed: %s", $instance->translator->translate("ERROR_COMMON_MESSAGE", ["CODE" => $e->getCode(), "MESSAGE" => $e->getMessage()])));
        }
        $data = json_encode($xml);
        $data = json_decode($data, true);
        if(isset($data["id"])) {
            $data = [$data];
        }
        $result = [];
        foreach ($data as $plan) {
            $result[Plesk_Object_Addon::ADDON_PREFIX . $plan["name"]] = $plan["name"];
        }
        return $result;
    }
    public function createValueAddedServices($existingAddons) : array
    {
        try {
            $newVas = [];
            foreach (static::VALUE_ADDED_SERVICES as $vasTemplate) {
                $vasName = $vasTemplate["name"];
                if(in_array($vasName, $existingAddons)) {
                } else {
                    $newVasItem = [];
                    $missingOrInactiveExtensionNames = [];
                    $unlicensedExtensionNames = [];
                    foreach ($vasTemplate["required_extensions"] as $requiredExtension) {
                        $extensionFriendlyName = static::EXTENSIONS[$requiredExtension]["display_name"];
                        if(!$this->isExtensionActive($requiredExtension)) {
                            $missingOrInactiveExtensionNames[] = $extensionFriendlyName;
                        }
                        if(!$this->isExtensionLicensed($requiredExtension)) {
                            $unlicensedExtensionNames[] = $extensionFriendlyName;
                        }
                    }
                    $errors = [];
                    if(!empty($missingOrInactiveExtensionNames)) {
                        $errors[] = sprintf("Required extensions are missing or inactive: %s", implode(",", $missingOrInactiveExtensionNames));
                    }
                    if(!empty($unlicensedExtensionNames)) {
                        $errors[] = sprintf("The following extensions are installed but require a license: %s", implode(",", $unlicensedExtensionNames));
                    }
                    if(empty($errors)) {
                        $permissions = array_map(function ($value) {
                            return !empty($value) ? "true" : "false";
                        }, $vasTemplate["permissions"]);
                        $apiData = ["name" => $vasName, "limits" => $vasTemplate["limits"], "permissions" => $permissions];
                        $xml = Plesk_Registry::getInstance()->manager->createServicePlanAddon($apiData);
                        $responseData = json_encode($xml);
                        $responseData = json_decode($responseData, true);
                        $newVasItem = ["id" => $responseData["guid"], "name" => $vasName];
                    } else {
                        $newVasItem["errors"] = $errors;
                    }
                    $newVas[$vasName] = $newVasItem;
                }
            }
            return array_merge($existingAddons, $newVas);
        } catch (Exception $e) {
            throw new WHMCS\Exception\Module\NotServicable("CreateValueAddedServices failed: " . Plesk_Registry::getInstance()->translator->translate("ERROR_COMMON_MESSAGE", ["CODE" => $e->getCode(), "MESSAGE" => $e->getMessage()]));
        }
    }
}

?>