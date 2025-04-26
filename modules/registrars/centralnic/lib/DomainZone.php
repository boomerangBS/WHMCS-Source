<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class DomainZone extends Domain
{
    protected $api;
    protected $zone = "";
    public function __construct(Api\ApiInterface $api, string $name)
    {
        $this->api = $api;
        parent::__construct($name);
    }
    public function getZone()
    {
        if(empty($this->zone)) {
            $this->zone = (new Commands\GetZone($this->api, $this->getName()))->execute()->getDataValue("zone");
        }
        return $this->zone;
    }
}

?>