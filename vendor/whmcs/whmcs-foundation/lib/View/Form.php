<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View;

class Form
{
    private $url = "";
    private $method = "";
    private $params = [];
    private $submitLabel = "";
    const METHOD_GET = "get";
    const METHOD_POST = "post";
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }
    public function setUriPrefixAdminBaseUrl($uri)
    {
        return $this->setUri(\WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl() . "/" . $uri);
    }
    public function setUriByRoutePath($routePath)
    {
        return $this->setUri(routePath($routePath));
    }
    public function getUri()
    {
        return $this->uri;
    }
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
    public function getMethod()
    {
        return $this->method;
    }
    public function setParameters(array $params)
    {
        $this->params = $params;
        return $this;
    }
    public function getParameters()
    {
        return $this->params;
    }
    public function setSubmitLabel($submitLabel)
    {
        $this->submitLabel = $submitLabel;
        return $this;
    }
    public function getSubmitLabel()
    {
        return $this->submitLabel;
    }
}

?>