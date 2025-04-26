<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module;

class RegistrarSetting extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblregistrars";
    public $timestamps = false;
    protected $fillable = ["registrar"];
    public function scopeRegistrar(\Illuminate\Database\Eloquent\Builder $query, $registrarName)
    {
        return $query->where("registrar", "=", $registrarName);
    }
    public function scopeSetting(\Illuminate\Database\Eloquent\Builder $query, $registrarSettingName)
    {
        return $query->where("setting", "=", $registrarSettingName);
    }
    public function getValueAttribute($value)
    {
        if(!empty($value)) {
            $value = $this->decrypt($value);
        }
        return $value;
    }
    public function setValueAttribute($value)
    {
        $this->attributes["value"] = $this->encrypt($value);
    }
}

?>