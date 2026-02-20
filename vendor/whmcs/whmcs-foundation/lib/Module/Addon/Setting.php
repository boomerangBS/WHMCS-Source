<?php

namespace WHMCS\Module\Addon;

class Setting extends \WHMCS\Model\AbstractModel
{
    protected $table = "tbladdonmodules";
    protected $fillable = ["module", "setting"];
    public $timestamps = false;
    const MODULE_LICENSING = "licensing";
    const SETTING_OPTIMISE_TABLE = "CronOptimizeTable";
    public static function getSettingValueForModule($module, string $setting)
    {
        $settingObject = self::module($module)->where("setting", "=", $setting)->first();
        if(!is_null($settingObject)) {
            return $settingObject->value;
        }
        return NULL;
    }
    public function scopeModule($query, $module)
    {
        return $query->where("module", $module);
    }
}

?>