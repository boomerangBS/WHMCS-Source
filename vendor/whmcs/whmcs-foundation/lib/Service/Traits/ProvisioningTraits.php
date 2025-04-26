<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Service\Traits;

trait ProvisioningTraits
{
    protected $moduleInterface;
    public function moduleInterface() : \WHMCS\Module\Server
    {
        if(is_null($this->moduleInterface)) {
            $this->moduleInterface = \WHMCS\Module\Server::factoryFromModel($this);
        }
        return $this->moduleInterface;
    }
    public function getMxRecords()
    {
        return $this->moduleInterface()->call("GetMxRecords", ["mxDomain" => $this->domain]);
    }
    public function addMxRecords($add) : \self
    {
        $this->moduleInterface()->call("AddMxRecords", $add);
        return $this;
    }
    public function removeMxRecords($remove = NULL, $serviceProperties) : \self
    {
        if($remove) {
            if(is_null($serviceProperties)) {
                $serviceProperties = $this->serviceProperties;
            }
            $this->moduleInterface()->call("DeleteMxRecords", ["mxDomain" => $this->domain, "mxRecords" => $remove]);
            $dataString = "";
            foreach ($remove as $datum) {
                $dataString .= $datum["priority"] . ":" . $datum["mx"] . "\r\n";
            }
            $serviceProperties->save(["Original MX Records" => ["type" => "textarea", "value" => $dataString]]);
        }
        return $this;
    }
    public function getSPFRecord() : array
    {
        return $this->moduleInterface()->call("GetSPFRecord", ["spfDomain" => $this->domain]);
    }
    public function setSPFRecord($record) : \self
    {
        $this->moduleInterface()->call("SetSPFRecord", ["spfDomain" => $this->domain, "spfRecord" => $record]);
        return $this;
    }
}

?>