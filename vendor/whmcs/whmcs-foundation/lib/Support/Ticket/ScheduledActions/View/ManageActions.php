<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions\View;

class ManageActions extends \WHMCS\View\Composite\CompositeView
{
    public function getTemplate()
    {
        return ScheduledActions::TEMPLATE_BASE_PATH . "/actionsPanel";
    }
    public function setAvailableActions($actionClasses) : \self
    {
        return $this->with(["availableActions" => collect($actionClasses)]);
    }
    public function init()
    {
        return parent::init()->setAvailableActions(collect((new \WHMCS\Support\Ticket\Actions\ActionsList())->getActions()));
    }
}

?>