<?php

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