<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Scheduling\Jobs;

trait JobTrait
{
    protected $jobName = "";
    protected $jobClassName = "";
    protected $jobMethodName = "";
    protected $jobMethodArguments = [];
    protected $jobDigestHash = "";
    protected $jobAvailableAt;
    public function jobName($name = "")
    {
        if($name) {
            $this->jobName = $name;
        }
        return $this->jobName;
    }
    public function jobClassName($className = "")
    {
        if($className) {
            $this->jobClassName = $className;
        } elseif(!$className && !$this->jobClassName) {
            $this->jobClassName = static::class;
        }
        return $this->jobClassName;
    }
    public function jobMethodName($methodName = "")
    {
        if($methodName) {
            $this->jobMethodName = $methodName;
        }
        return $this->jobMethodName;
    }
    public function jobMethodArguments($arguments = [])
    {
        if($arguments) {
            $this->jobMethodArguments = $arguments;
        }
        return $this->jobMethodArguments;
    }
    public function jobAvailableAt(\WHMCS\Carbon $date = NULL)
    {
        if($date) {
            $this->jobAvailableAt = $date;
        } elseif(!$date && !$this->jobAvailableAt) {
            $this->jobAvailableAt = \WHMCS\Carbon::now();
        }
        return $this->jobAvailableAt;
    }
    public function jobDigestHash($hash = "")
    {
        if($hash) {
            $this->jobDigestHash = $hash;
        }
        return $this->jobDigestHash;
    }
}

?>