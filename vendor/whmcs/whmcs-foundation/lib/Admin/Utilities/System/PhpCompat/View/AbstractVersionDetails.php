<?php

namespace WHMCS\Admin\Utilities\System\PhpCompat\View;

abstract class AbstractVersionDetails
{
    protected $style;
    protected $phpVersion = "";
    protected $phpVersionId = "";
    protected $iterator;
    protected $ioncubeLoader;
    protected $isPhpVersionSupported = false;
    protected $loaderVersionMinimum;
    public function __construct($phpVersion, $phpVersionId, $iterator, $ioncubeLoader, $whmcsCompat)
    {
        $this->setPhpVersion($phpVersion)->setPhpVersionId($phpVersionId)->setIterator($iterator)->setIoncubeLoader($ioncubeLoader)->setIsPhpVersionSupported($whmcsCompat);
    }
    public abstract function getHtml();
    public function getPhpVersion()
    {
        return $this->phpVersion;
    }
    public function setPhpVersion($phpVersion)
    {
        $this->phpVersion = $phpVersion;
        return $this;
    }
    public function getPhpVersionId()
    {
        return $this->phpVersionId;
    }
    public function setPhpVersionId($phpVersionId)
    {
        $this->phpVersionId = $phpVersionId;
        return $this;
    }
    public function getIterator()
    {
        return $this->iterator;
    }
    public function setIterator($iterator)
    {
        $this->iterator = $iterator;
        return $this;
    }
    public function getIoncubeLoader()
    {
        return $this->ioncubeLoader;
    }
    public function setIoncubeLoader($ioncubeLoader)
    {
        $this->ioncubeLoader = $ioncubeLoader;
        return $this;
    }
    public function setIsPhpVersionSupported($value) : \self
    {
        $this->isPhpVersionSupported = $value;
        return $this;
    }
    public function isPhpVersionSupported()
    {
        return $this->isPhpVersionSupported;
    }
    public function setRequiredMinimumLoaderVersion(\WHMCS\Version\SemanticVersion $version) : \self
    {
        $this->loaderVersionMinimum = $version;
        return $this;
    }
    public function getRequiredMinimumLoaderVersion() : \WHMCS\Version\SemanticVersion
    {
        return $this->loaderVersionMinimum;
    }
    public function isLoaderVersionSatisfied(\WHMCS\Version\SemanticVersion $target) : \WHMCS\Version\SemanticVersion
    {
        if(!$this->loaderVersionMinimum instanceof \WHMCS\Version\SemanticVersion) {
            return true;
        }
        return version_compare($target->getVersion(), $this->loaderVersionMinimum->getVersion(), ">=");
    }
}

?>