<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service;

class ServiceData extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblservicedata";
    protected $dates = ["expiresAt"];
    protected $columnMap = ["serviceId" => "service_id", "addonId" => "addon_id", "expiresAt" => "expires_at", "createdAt" => "created_at", "updatedAt" => "updated_at"];
    const ACTOR_ADMIN = "admin";
    const ACTOR_CLIENT = "client";
    const ACTOR_API = "api";
    const ACTOR_OTHER = "other";
    const ALL_ACTORS = NULL;
    public function createTable($drop = false)
    {
        $tableName = $this->table;
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($drop) {
            $schemaBuilder->dropIfExists($tableName);
        }
        if(!$schemaBuilder->hasTable($tableName)) {
            $schemaBuilder->create($tableName, function ($table) {
                $table->bigIncrements("id");
                $table->unsignedInteger("service_id")->nullable();
                $table->unsignedInteger("addon_id")->nullable();
                $table->unsignedInteger("client_id");
                $table->char("actor", 32)->nullable();
                $table->char("scope", 32);
                $table->char("name", 64);
                $table->char("value", 64)->nullable();
                $table->dateTime("expires_at")->nullable();
                $table->timestamps();
                $table->index("service_id");
                $table->index("addon_id");
                $table->index("client_id");
                $table->rawIndex(\WHMCS\Database\Capsule::raw("actor(16)"), "actor");
                $table->rawIndex(\WHMCS\Database\Capsule::raw("scope(16)"), "scope");
                $table->rawIndex(\WHMCS\Database\Capsule::raw("name(16)"), "name");
                $table->index("expires_at");
            });
        }
    }
    public static function boot()
    {
        parent::boot();
        self::saving(function (ServiceData $data) {
            if(is_null($data->serviceId) && is_null($data->addonId)) {
                throw new \WHMCS\Exception("A service data record must have either service ID or addon ID");
            }
        });
    }
    public function setService(Service $service) : \self
    {
        $this->serviceId = $service->id;
        $this->clientId = $service->clientId;
        return $this;
    }
    public function setAddon(Addon $addon) : \self
    {
        $this->addonId = $addon->id;
        if($addon->serviceId) {
            $this->serviceId = $addon->serviceId;
        }
        $this->clientId = $addon->clientId;
        return $this;
    }
    public function setServiceOrAddon($serviceOrAddon) : \self
    {
        if($serviceOrAddon instanceof Service) {
            return $this->setService($serviceOrAddon);
        }
        if($serviceOrAddon instanceof Addon) {
            return $this->setAddon($serviceOrAddon);
        }
        throw new \WHMCS\Exception("Invalid parent entity for service data");
    }
    public function setActor($actor) : \self
    {
        if(!is_null($actor) && !in_array($actor, self::ALL_ACTORS)) {
            throw new \WHMCS\Exception("Invalid actor for service data");
        }
        $this->actor = $actor;
        return $this;
    }
    public function setScope($scope) : \self
    {
        $this->scope = $scope;
        return $this;
    }
    public function setName($name) : \self
    {
        $this->name = $name;
        return $this;
    }
    public function setValue($value) : \self
    {
        $this->value = $value;
        return $this;
    }
    public function setExpiresAt(\WHMCS\Carbon $expiresAt) : \self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }
    public static function prune()
    {
        self::where("expires_at", "<", \WHMCS\Carbon::now())->delete();
    }
    public function service()
    {
        return $this->belongsTo("WHMCS\\Service\\Service", "service_id", "id", "service");
    }
    public function addon()
    {
        return $this->belongsTo("WHMCS\\Service\\Addon", "addon_id", "id", "addon");
    }
    public function scopeOfService(\Illuminate\Database\Eloquent\Builder $query, Service $service)
    {
        return $query->where("service_id", $service->id);
    }
    public function scopeOfAddon(\Illuminate\Database\Eloquent\Builder $query, Addon $addon)
    {
        return $query->where("addon_id", $addon->id);
    }
    public function scopeScope(\Illuminate\Database\Eloquent\Builder $query, string $scope)
    {
        return $query->where("scope", $scope);
    }
}

?>