<?php

namespace WHMCS\Product\Server;

class Group extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblservergroups";
    protected $columnMap = ["fillType" => "filltype"];
    public $timestamps = false;
    const FILL_TYPE_ROUND_ROBIN = 1;
    const FILL_TYPE_FILL = 2;
    public function servers()
    {
        return $this->belongsToMany("WHMCS\\Product\\Server", "tblservergroupsrel", "groupid", "serverid", "id", "id", "servers");
    }
    public function serverRelationPivot()
    {
        return $this->hasMany("WHMCS\\Product\\Server\\Relations\\ServerGroup", "groupid");
    }
    public function getDefaultServer() : \WHMCS\Product\Server
    {
        $defaultServer = $this->servers->first(function (\WHMCS\Product\Server $server) {
            return $server->active;
        });
        return $defaultServer;
    }
}

?>