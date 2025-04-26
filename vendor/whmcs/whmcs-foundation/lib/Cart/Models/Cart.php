<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cart\Models;

class Cart extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblcarts";
    protected $columnMap = [];
    public function createTable($drop = false)
    {
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($drop) {
            $schemaBuilder->dropIfExists($this->getTable());
        }
        if(!$schemaBuilder->hasTable($this->getTable())) {
            $schemaBuilder->create($this->getTable(), function ($table) {
                $table->increments("id");
                $table->unsignedInteger("user_id")->nullable();
                $table->char("tag", 64);
                $table->text("data");
                $table->timestamp("created_at")->default("0000-00-00 00:00:00");
                $table->timestamp("updated_at")->default("0000-00-00 00:00:00");
                $table->unique("tag");
            });
        }
    }
    public static function boot()
    {
        parent::boot();
        static::saving(function (Cart $cart) {
            if(is_null($cart->tag)) {
                $maxTries = 100;
                do {
                    $cart->tag = \Illuminate\Support\Str::random(16);
                } while (!(static::byTag($cart->tag)->exists() && 0 < $maxTries--));
            }
        });
    }
    public function getDataAttribute() : array
    {
        if(isset($this->attributes["data"])) {
            $decoded = json_decode($this->attributes["data"], true);
            if($decoded) {
                return $decoded;
            }
        }
        return [];
    }
    public function setDataAttribute(array $value)
    {
        $this->attributes["data"] = json_encode($value);
    }
    public function user()
    {
        return $this->belongsTo("WHMCS\\User\\User", "user_id", "id", "user");
    }
    public function features()
    {
        return $this->hasMany("WHMCS\\Product\\Group\\Feature", "product_group_id")->orderBy("order");
    }
    public function scopeByTag($query, string $tag)
    {
        return $query->where("tag", $tag);
    }
    public function scopeByUser($query, $user) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("user_id", $user->id);
    }
    public function exportToSession() : void
    {
        \WHMCS\Session::set("cart", $this->data);
    }
    public function importFromSession() : void
    {
        $this->data = (new \WHMCS\OrderForm())->getCartData();
    }
}

?>