<?php

namespace WHMCS\Product;

class OnDemandRenewal extends \WHMCS\Model\AbstractModel
{
    use \WHMCS\Model\HasProductEntityTrait;
    protected $table = "tblondemandrenewals";
    protected $columnMap = ["id" => "id", "relType" => "rel_type", "relId" => "rel_id", "enabled" => "enabled", "monthly" => "monthly", "quarterly" => "quarterly", "semiannually" => "semiannually", "biennially" => "biennially", "triennially" => "triennially"];
    public $timestamps = false;
    protected $booleans = ["enabled"];
    const ON_DEMAND_RENEWAL_TYPE_PRODUCT = "Product";
    const ON_DEMAND_RENEWAL_TYPE_ADDON = "Addon";
    const ON_DEMAND_RENEWAL_PERIOD_MAX_MONTHLY = "31";
    const ON_DEMAND_RENEWAL_PERIOD_MAX_QUARTERLY = "92";
    const ON_DEMAND_RENEWAL_PERIOD_MAX_SEMIANNUALLY = "184";
    const ON_DEMAND_RENEWAL_PERIOD_MAX_ANNUALLY = "366";
    const ON_DEMAND_RENEWAL_PERIOD_MAX_BIENNIALLY = "731";
    const ON_DEMAND_RENEWAL_PERIOD_MAX_TRIENNIALLY = "1096";
    const ON_DEMAND_RENEWAL_TYPES = NULL;
    public function createTable($drop = false)
    {
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($drop) {
            $schemaBuilder->dropIfExists($this->getTable());
        }
        if(!$schemaBuilder->hasTable($this->getTable())) {
            $schemaBuilder->create($this->getTable(), function ($table) {
                $table->increments("id");
                $table->enum("rel_type", self::ON_DEMAND_RENEWAL_TYPES);
                $table->integer("rel_id")->default(0);
                $table->tinyInteger("enabled")->default(0);
                $table->tinyInteger("monthly")->default(0);
                $table->tinyInteger("quarterly")->default(0);
                $table->smallInteger("semiannually")->default(0);
                $table->smallInteger("annually")->default(0);
                $table->smallInteger("biennially")->default(0);
                $table->smallInteger("triennially")->default(0);
                $table->unique(["rel_type", "rel_id"], "tblondemandrenewals_rel_type_rel_id_unique");
            });
        }
    }
    public static function boot()
    {
        parent::boot();
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([self::ON_DEMAND_RENEWAL_TYPE_PRODUCT => "WHMCS\\Product\\Product", self::ON_DEMAND_RENEWAL_TYPE_ADDON => "WHMCS\\Product\\Addon"]);
    }
    public function getSupportedTypes() : array
    {
        return self::ON_DEMAND_RENEWAL_TYPES;
    }
    public static function findOrCreate($relType, int $relId) : OnDemandRenewal
    {
        $onDemand = OnDemandRenewal::where("rel_type", "=", $relType)->where("rel_id", "=", $relId)->first();
        if(is_null($onDemand)) {
            $onDemand = new OnDemandRenewal();
            $onDemand->relType = $relType;
            $onDemand->relId = $relId;
            $onDemand->save();
        }
        return $onDemand;
    }
}

?>