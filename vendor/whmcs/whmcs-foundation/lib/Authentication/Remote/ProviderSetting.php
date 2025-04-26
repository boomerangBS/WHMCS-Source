<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Authentication\Remote;

class ProviderSetting extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblauthn_config";
    public function createTable($drop = false)
    {
        $schemaBuilder = \Illuminate\Database\Capsule\Manager::schema();
        if($drop) {
            $schemaBuilder->dropIfExists($this->getTable());
        }
        if(!$schemaBuilder->hasTable($this->getTable())) {
            $schemaBuilder->create($this->getTable(), function ($table) {
                $table->increments("id");
                $table->char("provider", 64);
                $table->char("setting", 128);
                $table->text("value")->nullable();
                $table->nullableTimestamps();
                $table->unique(["provider", "setting"]);
            });
        }
    }
    public function scopeForProvider(\Illuminate\Database\Eloquent\Builder $query, Providers\AbstractRemoteAuthProvider $provider)
    {
        return $query->where("provider", "=", $provider::NAME);
    }
    public function scopeEnabled(\Illuminate\Database\Eloquent\Builder $query)
    {
        return $query->where("setting", "Enabled")->where("value", "1");
    }
}

?>