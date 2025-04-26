<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Template;

class AssetUtil
{
    private $template;
    private $templateName = "";
    private $configValues;
    private static $cachePaths = [];
    private static $cacheDisk = [];
    public function __construct(TemplateSetInterface $template)
    {
        $this->template = $template;
        $this->templateName = $template->getName();
        $this->configValues = $template->getTemplateConfigValues();
    }
    public static function resetCache()
    {
        self::$cachePaths = [];
        self::$cacheDisk = [];
    }
    protected function cachePath($templateName, string $namespace, string $basename, string $path) : void
    {
        self::$cachePaths[$templateName][$namespace][$basename] = $path;
    }
    protected function cacheDisk($templateName, string $namespace, string $basename = false, $exists) : void
    {
        if(!$namespace) {
            self::$cacheDisk[$templateName][$basename] = $exists;
        } else {
            self::$cacheDisk[$templateName][$namespace][$basename] = $exists;
        }
    }
    protected function isCached($namespace, string $basename)
    {
        if(!$namespace) {
            return (bool) isset(self::$cachePaths[$this->templateName][$basename]);
        }
        return (bool) isset(self::$cachePaths[$this->templateName][$namespace][$basename]);
    }
    protected function onDiskStatus($namespace, string $basename)
    {
        if(!$namespace && isset(self::$cacheDisk[$this->templateName][$basename])) {
            return (bool) self::$cacheDisk[$this->templateName][$basename];
        }
        if(isset(self::$cacheDisk[$this->templateName][$namespace][$basename])) {
            return (bool) isset(self::$cacheDisk[$this->templateName][$namespace][$basename]);
        }
        return NULL;
    }
    protected function cachedValue($namespace, string $basename)
    {
        if(!$namespace) {
            return self::$cachePaths[$this->templateName][$basename];
        }
        return self::$cachePaths[$this->templateName][$namespace][$basename];
    }
    protected function exists(string $namespace, string $basename)
    {
        $cache = $this->onDiskStatus($namespace, $basename);
        if(!is_null($cache)) {
            return $cache;
        }
        $namespacePath = "";
        if($namespace) {
            $namespacePath = DIRECTORY_SEPARATOR . $namespace;
        }
        $templateValues = $this->configValues;
        $path = $templateValues->assetDirectory() . $namespacePath . DIRECTORY_SEPARATOR . $basename;
        $status = (bool) file_exists($path);
        self::cacheDisk($this->templateName, $namespace, $basename, $status);
        return $status;
    }
    public static function factoryThemeUtil() : \self
    {
        return new static(Theme::factory());
    }
    public static function factoryOrderformUtil() : \self
    {
        return new static(OrderForm::factory());
    }
    public function assetPaths()
    {
        return $this->template->getTemplateConfigValues()->assetPaths();
    }
    public function assetUrl($basename = NULL, string $namespace)
    {
        if(empty($namespace)) {
            $namespace = pathinfo($basename, PATHINFO_EXTENSION);
        }
        if(empty($basename) || empty($namespace)) {
            return "";
        }
        if($namespace === "tpl") {
            $namespace = "";
        }
        $path = $this->assetExists($basename, $namespace);
        if(!$path) {
            $templateValues = $this->configValues;
            $parent = $templateValues->getParent();
            if($parent) {
                $path = (new static($parent->getTemplate()))->assetUrl($basename, $namespace);
            } else {
                $path = $templateValues->templateUrlPath() . ($namespace ? "/" . $namespace : "") . "/" . $basename;
            }
        }
        return $path;
    }
    public function assetExists(string $basename, string $namespace = NULL)
    {
        $path = "";
        if(empty($namespace)) {
            $namespace = pathinfo($basename, PATHINFO_EXTENSION);
        }
        if(empty($basename) || empty($namespace)) {
            return false;
        }
        if($namespace === "tpl") {
            $namespace = "";
        }
        if($this->isCached($namespace, $basename)) {
            return $this->cachedValue($namespace, $basename);
        }
        $templateValues = $this->configValues;
        $parent = $templateValues->getParent();
        $customNamespace = $templateValues->getAssetPathDeclaration($namespace);
        if(!empty($customNamespace)) {
            if($parent && \WHMCS\Config\Template::isParent($customNamespace)) {
                return (new self($parent->getTemplate()))->assetExists($basename, $namespace);
            }
            if(\WHMCS\Config\Template::isSystemPath($customNamespace)) {
            } elseif(\WHMCS\Config\Template::isExternalUrl($customNamespace)) {
            }
        }
        if($customNamespace && $this->exists($customNamespace, $basename)) {
            $path = $templateValues->templateUrlPath() . "/" . $customNamespace . "/" . $basename;
        } elseif($this->exists($namespace, $basename)) {
            $path = $templateValues->templateUrlPath() . ($namespace ? "/" . $namespace : "") . "/" . $basename;
        }
        if($path) {
            self::cachePath($this->templateName, $namespace, $basename, $path);
            return $path;
        }
        if($parent) {
            return (new self($parent->getTemplate()))->assetExists($basename, $namespace);
        }
        return false;
    }
}

?>