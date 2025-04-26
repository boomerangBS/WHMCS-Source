<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Config;

class SettingCollection extends \Illuminate\Database\Eloquent\Collection
{
    protected $prefix;
    protected $modelClassName;
    public function __construct($models = [], $modelClassName = "\\WHMCS\\Config\\Setting", $prefix = "")
    {
        $this->prefix = $prefix;
        $this->modelClassName = $modelClassName ?: "\\WHMCS\\Config\\Setting";
        $items = [];
        foreach ($models as $item) {
            $items[] = $item;
        }
        parent::__construct($items);
    }
    public function __set($key, $value)
    {
        if($item = $this->find($key)) {
            $item->value = $value;
        } elseif($item = $this->find($this->prefix . $key)) {
            $item->value = $value;
        } else {
            $item = $this->baseModel();
            $item->setting = $this->prefix . $key;
            $item->value = $value;
            $this->add($item);
        }
    }
    public function __get($key)
    {
        if($item = $this->find($key)) {
            return $item->value;
        }
        if($item = $this->find($this->prefix . $key)) {
            return $item->value;
        }
    }
    public function find($key, $default = NULL)
    {
        if($key instanceof \Illuminate\Database\Eloquent\Model) {
            $key = $key->getKey();
        }
        if($this->prefix && strpos($key, $this->prefix) !== 0) {
            $fullKey = $this->prefix . $key;
        } else {
            $fullKey = $key;
        }
        return parent::find($fullKey, $default);
    }
    public function saveAll()
    {
        $failedToSaveAll = false;
        $this->each(function ($item) use($failedToSaveAll) {
            if($item->save() === false) {
                $failedToSaveAll = true;
            }
        });
        return !$failedToSaveAll;
    }
    public function baseModel()
    {
        $model = $this->modelClassName;
        return new $model();
    }
}

?>