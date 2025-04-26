<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product;

class ConfigOption extends \WHMCS\Model\AbstractModel
{
    use CompoundNameTrait;
    protected $table = "tblproductconfigoptions";
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $fillable = ["gid", "optionname", "optiontype", "qtyminimum", "qtymaximum", "order", "hidden"];
    protected $casts = ["gid" => "integer", "optionname" => "string", "optiontype" => "integer", "qtyminimum" => "integer", "qtymaximum" => "integer", "order" => "integer", "hidden" => "boolean"];
    protected $columnMap = ["groupId" => "gid", "isHidden" => "hidden"];
    protected $selectableOptionClass = "WHMCS\\Product\\ConfigOptionSelection";
    protected $configGroupClass = "WHMCS\\Product\\ConfigOptionGroup";
    public function selectableOptions()
    {
        return $this->hasMany($this->selectableOptionClass, "configid", "id");
    }
    public function configGroup()
    {
        return $this->belongsTo($this->configGroupClass, "gid", "id", "configGroup");
    }
    public function scopeOfProduct($query, Product $product)
    {
        return $query->ofProductId($product->id);
    }
    public function scopeOfProductId($query, $productId)
    {
        return $query->whereIn("gid", ConfigOptionGroupLinks::productId($productId)->pluck("gid"));
    }
}

?>