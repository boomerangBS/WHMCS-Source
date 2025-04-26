<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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