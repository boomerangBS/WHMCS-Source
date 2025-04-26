<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product\Server;

class Remote extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblservers_remote";
    protected $columnMap = ["serverId" => "server_id", "numAccounts" => "num_accounts", "metaData" => "meta_data"];
    protected $fillable = ["server_id"];
    public function server()
    {
        return $this->belongsTo("WHMCS\\Product\\Server", "server_id", "id", "server");
    }
    public function getMetaDataAttribute($metaData)
    {
        $return = $metaData;
        if(!is_array($return)) {
            $return = json_decode($metaData, true);
        }
        if(!is_array($return)) {
            $return = [];
        }
        return $return;
    }
    public function setMetaDataAttribute($metaData)
    {
        if(is_array($metaData)) {
            $metaData = json_encode($metaData);
        }
        if(!$metaData) {
            $metaData = "{}";
        }
        $this->attributes["meta_data"] = $metaData;
    }
}

?>