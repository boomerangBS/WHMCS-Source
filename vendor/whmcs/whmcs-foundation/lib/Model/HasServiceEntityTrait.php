<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Model;

trait HasServiceEntityTrait
{
    protected static $serviceRelationTypes;
    public static function loadRelationClassMap()
    {
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap(self::$serviceRelationTypes);
    }
    public function relationEntity()
    {
        return $this->morphTo("rel");
    }
    public function scopeOfRelated($query, $relation)
    {
        $relType = get_class($relation);
        foreach (self::$serviceRelationTypes as $type => $baseClass) {
            if($relation instanceof $baseClass) {
                $relType = $type;
            }
        }
        return $query->where("rel_type", $relType)->where("rel_id", $relation->id);
    }
}

?>