<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Model;

trait HasModuleTrait
{
    protected $instantiatedModule;
    public function scopeOfModule($query, \WHMCS\Module\AbstractModule $module)
    {
        return $query->where("module_type", $module->getType())->where("module", $module->getLoadedModule());
    }
    public function getModuleNameAttribute()
    {
        return $this->getRawAttribute("module");
    }
    public function setModuleNameAttribute($value)
    {
        $this->attributes["module"] = $value;
    }
    public function getModuleTypeAttribute()
    {
        return $this->getRawAttribute("module_type");
    }
    public function setModuleTypeAttribute($value)
    {
        $this->attributes["module_type"] = $value;
    }
    public function setModuleAttribute($value)
    {
        $this->instantiatedModule = $name = $type = NULL;
        unset($this->attributes["module"]);
        if($value instanceof \WHMCS\Module\AbstractModule) {
            $type = $value->getType();
            $name = $value->getLoadedModule();
        } elseif(is_string($value)) {
            if(strpos($value, "|") !== false) {
                list($type, $name) = explode("|", $value, 2);
            } else {
                $this->attributes["module"] = $value;
            }
        }
        if($type && $name) {
            $this->attributes["module"] = $name;
            $this->attributes["module_type"] = $type;
            $this->instantiatedModule = $value;
        }
    }
    public function getModuleAttribute()
    {
        if($this->instantiatedModule) {
            return $this->instantiatedModule;
        }
        $type = $this->getRawAttribute("module_type");
        $name = $this->getRawAttribute("module");
        if($type && $name) {
            $moduleHelper = new \WHMCS\Module\Module();
            try {
                $class = $moduleHelper->getClassByModuleType($type);
                $module = new $class();
                if($module->load($name)) {
                    $this->instantiatedModule = $module;
                    return $module;
                }
            } catch (\Exception $e) {
            }
        }
    }
}

?>