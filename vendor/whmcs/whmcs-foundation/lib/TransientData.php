<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
class TransientData
{
    protected $chunkSize = 62000;
    const DB_TABLE = "tbltransientdata";
    const DEFAULT_TTL_SECONDS = 300;
    public static function getInstance() : TransientData
    {
        return new self();
    }
    protected function getCurrentTimestamp() : int
    {
        return time();
    }
    public function store($name, string $data = self::DEFAULT_TTL_SECONDS, int $life) : int
    {
        $expires = $this->getCurrentTimestamp() + $life;
        if($this->getQueryByName($name, true)->exists()) {
            $this->updateByName($name, $data, $expires);
        } else {
            $this->insert($name, $data, $expires);
        }
        return true;
    }
    public function chunkedStore($name, string $data = self::DEFAULT_TTL_SECONDS, int $life) : int
    {
        $expires = $this->getCurrentTimestamp() + $life;
        $this->clearChunkedStorage($name);
        $i = 0;
        for ($remainingData = $data; $remainingData !== ""; $i++) {
            $this->insert($name . ".chunk-" . $i, substr($remainingData, 0, $this->chunkSize), $expires);
            $remainingData = (string) substr($remainingData, $this->chunkSize);
        }
        return true;
    }
    protected function clearChunkedStorage($name)
    {
        Database\Capsule::table(self::DB_TABLE)->where("name", "LIKE", $name . ".chunk-%")->delete();
    }
    public function retrieve($name)
    {
        return $this->getQueryByName($name, false)->value("data");
    }
    public function retrieveChunkedItem($name)
    {
        $data = Database\Capsule::table(self::DB_TABLE)->where("name", "LIKE", $name . ".chunk-%")->where("expires", ">", $this->getCurrentTimestamp())->pluck("data")->all();
        if(0 < count($data)) {
            return implode($data);
        }
        return NULL;
    }
    public function retrieveByData($data)
    {
        $query = Database\Capsule::table(self::DB_TABLE)->where("data", "=", $data)->where("expires", ">", $this->getCurrentTimestamp());
        return $query->value("name");
    }
    public function delete($name)
    {
        return (bool) Database\Capsule::table(self::DB_TABLE)->where("name", $name)->delete();
    }
    public function purgeExpired($delaySeconds) : int
    {
        $now = $this->getCurrentTimestamp() - $delaySeconds;
        return (bool) Database\Capsule::table(self::DB_TABLE)->where("expires", "<", $now)->delete();
    }
    protected function getQueryByName($name, $includeExpired) : \Illuminate\Database\Query\Builder
    {
        $query = Database\Capsule::table(self::DB_TABLE)->where("name", $name);
        if(!$includeExpired) {
            $query->where("expires", ">", $this->getCurrentTimestamp());
        }
        return $query;
    }
    protected function insert($name, string $data, int $expires) : int
    {
        return Database\Capsule::table(self::DB_TABLE)->insertGetId(["name" => $name, "data" => $data, "expires" => $expires]);
    }
    protected function updateByName($name, string $data, int $expires) : int
    {
        return (bool) Database\Capsule::table(self::DB_TABLE)->where("name", $name)->update(["data" => $data, "expires" => $expires]);
    }
}

?>