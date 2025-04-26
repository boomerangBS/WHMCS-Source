<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Knowledgebase;

class Tag extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblknowledgebasetags";
    public $timestamps = false;
    public function scopeTag($query, $tag)
    {
        return $query->where("tag", "like", $tag);
    }
    public static function getTagTotals()
    {
        return static::select("tag", \WHMCS\Database\Capsule::raw("count(*) as total"))->groupBy("tag")->pluck("total", "tag")->all();
    }
    public function articles()
    {
        return $this->hasOne("\\WHMCS\\Knowledgebase\\Article", "articleid");
    }
}

?>