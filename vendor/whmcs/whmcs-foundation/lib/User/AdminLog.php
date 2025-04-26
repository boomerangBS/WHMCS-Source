<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User;

class AdminLog extends \WHMCS\Model\AbstractModel
{
    protected $table = "tbladminlog";
    protected $columnMap = ["username" => "adminusername"];
    public $timestamps = false;
    public $unique = ["sessionid"];
    public function admin()
    {
        return $this->belongsTo("\\WHMCS\\User\\Admin", "adminusername", "username", "admin");
    }
    public function scopeOnline($query)
    {
        return $query->where("lastvisit", ">", \WHMCS\Carbon::now()->subMinutes(15))->groupBy("adminusername")->orderBy("lastvisit");
    }
}

?>