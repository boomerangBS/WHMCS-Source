<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Product\EventAction;

class AddonEventActionProcessor extends AbstractEventActionProcessor
{
    public function __construct(\WHMCS\Service\Addon $entity)
    {
        parent::__construct($entity);
    }
    public function getScheduledEventActions($eventName) : array
    {
        return EventAction::ofAddon($this->entity->productAddon)->onEvent($eventName)->get()->all();
    }
    protected function getModuleHandler()
    {
        return new \WHMCS\Addon($this->entity->id);
    }
}

?>