<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html;

class GatewayConfiguration extends AbstractNoEngine
{
    protected $containerPrefix = "";
    protected $containerSuffix = "";
    protected $bodyPrefix = "";
    protected $bodySuffix = "";
    public function appendContainerPrefix($markup) : \self
    {
        $this->containerPrefix .= $markup;
        return $this;
    }
    public function getContainerPrefix()
    {
        return $this->containerPrefix;
    }
    public function appendContainerSuffix($markup) : \self
    {
        $this->containerSuffix .= $markup;
        return $this;
    }
    public function getContainerSuffix()
    {
        return $this->containerSuffix;
    }
    public function appendBodyPrefix($markup) : \self
    {
        $this->bodyPrefix .= $markup;
        return $this;
    }
    public function getBodyPrefix()
    {
        return $this->bodyPrefix;
    }
    public function appendBodySuffix($markup) : \self
    {
        $this->bodySuffix .= $markup;
        return $this;
    }
    public function getBodySuffix()
    {
        return $this->bodySuffix;
    }
}

?>