<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\Actions;

abstract class AbstractAction
{
    protected $ticket;
    public static $name = "";
    protected $attributeToAdminId = 0;
    public abstract function execute();
    public abstract function init(\WHMCS\Support\Ticket $ticket, array $parameters);
    public abstract function unserializeParameters($string) : array;
    public abstract function serializeParameters();
    public abstract function assertParameters(array $parameters);
    public static function factoryByName(string $action)
    {
        $actionClass = ActionsList::getActionClass($action);
        if(method_exists($actionClass, "factory")) {
            return $actionClass::factory();
        }
        return new $actionClass();
    }
    protected function getParametersToSerialize() : array
    {
        return $this->getPublicPropertyMap();
    }
    protected function getPublicPropertyMap() : array
    {
        return collect((new \ReflectionObject($this))->getProperties(\ReflectionProperty::IS_PUBLIC))->filter(function (\ReflectionProperty $property) {
            return !$property->isStatic();
        })->mapWithKeys(function (\ReflectionProperty $property) {
            return [$property->getName() => $property->getValue($this)];
        })->toArray();
    }
    public function getTicket() : \WHMCS\Support\Ticket
    {
        return $this->ticket;
    }
    public static function name()
    {
        return static::$name;
    }
    public function detailString()
    {
        return "";
    }
    public function attributeToAdmin($id) : \self
    {
        $this->attributeToAdminId = $id;
        return $this;
    }
    public function attributionAdmin() : \WHMCS\User\Admin
    {
        return \WHMCS\User\Admin::find($this->attributeToAdminId);
    }
    public static function overlayMapOnObject($map, $o)
    {
        $map = (object) $map;
        foreach (get_object_vars($o) as $property => $v) {
            if(property_exists($map, $property)) {
                $o->{$property} = $map->{$property};
            }
        }
        return $o;
    }
    public function displayName()
    {
        return \AdminLang::trans(sprintf("support.ticket.action.name.%s", strtolower(static::name())));
    }
}

?>