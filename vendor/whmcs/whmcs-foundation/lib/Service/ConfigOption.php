<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service;

class ConfigOption extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblhostingconfigoptions";
    public $timestamps = false;
    public function scopeOfService($query, Service $service)
    {
        return $query->where("relid", $service->id);
    }
    public function productConfigOptionSelection()
    {
        return $this->hasOne("WHMCS\\Product\\ConfigOptionSelection", "id", "optionid");
    }
    public function productConfigOption()
    {
        return $this->hasOne("WHMCS\\Product\\ConfigOption", "id", "configid");
    }
    public function service()
    {
        return $this->hasOne("WHMCS\\Service\\Service", "id", "relid");
    }
    public function metricUsage()
    {
        return $this->hasMany("WHMCS\\UsageBilling\\Service\\MetricUsage", "rel_id", "id")->where("rel_type", \WHMCS\Contracts\ProductServiceTypes::TYPE_SERVICE_CONFIGOPTION);
    }
}

?>