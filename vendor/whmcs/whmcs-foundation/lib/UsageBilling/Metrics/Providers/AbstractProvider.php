<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\UsageBilling\Metrics\Providers;

abstract class AbstractProvider implements \WHMCS\UsageBilling\Contracts\Metrics\ProviderInterface
{
    protected $storage = [];
    protected $metrics;
    public abstract function usage();
    public abstract function tenantUsage($tenant);
    public function __construct(array $metrics = [])
    {
        $data = [];
        foreach ($metrics as $v) {
            if($v instanceof \WHMCS\UsageBilling\Contracts\Metrics\MetricInterface) {
                $data[$v->systemName()] = $v;
            }
        }
        $this->metrics = $data;
    }
    public function metrics()
    {
        return $this->metrics;
    }
    protected function getStorage()
    {
        return $this->storage;
    }
    protected function setStorage(array $storage)
    {
        $this->storage = $storage;
        return $this;
    }
}

?>