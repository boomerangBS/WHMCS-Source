<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\File\Migration;

class FileAssetMigrationProgress extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblfileassetmigrationprogress";
    protected $casts = ["migrated_objects" => "array", "active" => "boolean"];
    protected $fillable = ["asset_type"];
    protected $attributes = ["active" => true];
    const MAX_CONSECUTIVE_FAILURES = 5;
    public function setAssetTypeAttribute($value)
    {
        if(!array_key_exists($value, \WHMCS\File\FileAsset::TYPES)) {
            throw new \WHMCS\Exception\Storage\StorageException("Invalid storage asset type: " . $value);
        }
        $this->attributes["asset_type"] = $value;
    }
    public function scopeForAssetType(\Illuminate\Database\Eloquent\Builder $query, $assetType)
    {
        if(!array_key_exists($assetType, \WHMCS\File\FileAsset::TYPES)) {
            throw new \WHMCS\Exception\Storage\StorageException("Invalid storage asset type: " . $assetType);
        }
        return $query->where("asset_type", $assetType);
    }
}

?>