<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment;

class Report
{
    private $type;
    private $components;
    const VERSION = "1.0.0";
    const API_SPEC = "1.0.0";
    const TYPE_EVENT = 0;
    const TYPE_STATE = 1;
    public function __construct($type, array $components = [])
    {
        $this->type = $type;
        $this->components = $components;
    }
    public function report()
    {
        return ["info" => $this->info(), "data" => $this->data(), "api" => $this->apiSpec()];
    }
    public function toJson()
    {
        return json_encode($this->report());
    }
    public function info()
    {
        return ["type" => $this->type(), "systemId" => $this->systemId(), "version" => $this->version()];
    }
    public function data()
    {
        $data = [];
        foreach ($this->components as $component) {
            if($component instanceof ComponentInterface) {
                $data[] = $component->report($this);
            }
        }
        return $data;
    }
    public function type()
    {
        return $this->type;
    }
    public function systemId()
    {
        return WHMCS::systemId();
    }
    public function apiSpec()
    {
        return static::API_SPEC;
    }
    public function version()
    {
        return static::VERSION;
    }
}

?>