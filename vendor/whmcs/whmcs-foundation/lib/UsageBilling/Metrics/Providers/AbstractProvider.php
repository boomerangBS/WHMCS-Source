<?php

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