<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\View\Admin\HealthCheck;

// Decoded file for php version 72.
class ApplicationDirectory
{
    public $friendlyName;
    public $currentPath;
    public $defaultPath;
    protected $filesystem;
    protected $when;
    protected $ignoreConcerns = [];
    public function __construct(string $currentPath)
    {
        $this->currentPath = $currentPath;
        $this->filesystem = new \WHMCS\File\Filesystem(new \League\Flysystem\Adapter\Local(dirname($currentPath)));
        $this->when = ["missing" => NULL, "not-writable" => NULL];
    }
    public function default($path) : \self
    {
        $this->defaultPath = $path;
        return $this;
    }
    public function name($name) : \self
    {
        $this->friendlyName = $name;
        return $this;
    }
    public function whenMissing($callable) : \self
    {
        $this->when["missing"] = $callable;
        return $this;
    }
    public function whenNotWritable($callable) : \self
    {
        $this->when["not-writable"] = $callable;
        return $this;
    }
    public function ignoreConcerns($circumstances) : \self
    {
        $this->ignoreConcerns = array_flip($circumstances);
        return $this;
    }
    public function isConcern($circumstance)
    {
        return !isset($this->ignoreConcerns[$circumstance]);
    }
    public function isDefault()
    {
        return $this->currentPath === $this->defaultPath;
    }
    public function exists()
    {
        return $this->filesystem->has(basename($this->currentPath));
    }
    public function writable()
    {
        return is_writable($this->currentPath);
    }
    public function invokeMissing()
    {
        return $this->invoke("missing");
    }
    public function invokeNotWritable()
    {
        return $this->invoke("not-writable");
    }
    public function invoke($circumstance)
    {
        if(is_callable($this->when[$circumstance])) {
            return call_user_func($this->when[$circumstance], $this);
        }
        return $this->defaultDisplay();
    }
    public function defaultDisplay()
    {
        return sprintf("%s (%s)", $this->friendlyName, $this->currentPath);
    }
}

?>