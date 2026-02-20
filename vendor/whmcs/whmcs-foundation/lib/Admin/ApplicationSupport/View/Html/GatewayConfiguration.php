<?php

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