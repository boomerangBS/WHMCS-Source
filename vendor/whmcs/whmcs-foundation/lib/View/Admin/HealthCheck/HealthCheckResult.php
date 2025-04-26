<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\View\Admin\HealthCheck;

// Decoded file for php version 72.
class HealthCheckResult
{
    protected $name;
    protected $type;
    protected $title;
    protected $severityLevel;
    protected $body;
    public function __construct($name, $type, $title, $severityLevel, $body)
    {
        $this->setName($name)->setType($type)->setTitle($title)->setSeverityLevel($severityLevel)->setBody($body);
    }
    public function getName()
    {
        return $this->name;
    }
    protected function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    public function getType()
    {
        return $this->type;
    }
    protected function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    public function getTitle()
    {
        return $this->title;
    }
    protected function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }
    public function getSeverityLevel()
    {
        return $this->severityLevel;
    }
    protected function setSeverityLevel($severityLevel)
    {
        if(!in_array($severityLevel, [\Psr\Log\LogLevel::EMERGENCY, \Psr\Log\LogLevel::ALERT, \Psr\Log\LogLevel::CRITICAL, \Psr\Log\LogLevel::ERROR, \Psr\Log\LogLevel::WARNING, \Psr\Log\LogLevel::NOTICE, \Psr\Log\LogLevel::INFO, \Psr\Log\LogLevel::DEBUG])) {
            throw new \WHMCS\Exception("Please provide a valid PSR-3 log level");
        }
        $this->severityLevel = $severityLevel;
        return $this;
    }
    public function getBody()
    {
        return $this->body;
    }
    protected function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
    public function toArray()
    {
        return ["name" => $this->getName(), "type" => $this->getType(), "severityLevel" => $this->getSeverityLevel(), "body" => $this->getBody()];
    }
}

?>