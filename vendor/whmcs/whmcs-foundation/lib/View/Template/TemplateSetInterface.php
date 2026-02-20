<?php

namespace WHMCS\View\Template;

interface TemplateSetInterface
{
    public static function type();
    public static function find($name) : \self;
    public static function all() : \Illuminate\Support\Collection;
    public static function defaultName();
    public static function defaultSettingKey();
    public function getName();
    public function getDisplayName();
    public static function getDefault() : TemplateSetInterface;
    public static function setDefault($value) : void;
    public function getConfig() : \WHMCS\Config\Template;
    public function isDefault();
    public function getParent() : TemplateSetInterface;
    public function isRoot();
    public function getProvides() : array;
    public function getDependencies() : array;
    public function getProperties() : array;
    public function getTemplatePath();
    public static function templateDirectory();
    public function resolveFilePath($basename);
    public function getTemplateConfigValues() : AbstractConfigValues;
}

?>