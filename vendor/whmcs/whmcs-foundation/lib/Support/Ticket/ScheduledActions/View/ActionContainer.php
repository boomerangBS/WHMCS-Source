<?php

namespace WHMCS\Support\Ticket\ScheduledActions\View;

class ActionContainer extends \WHMCS\View\Composite\CompositeView
{
    protected $action;
    public function __construct($action)
    {
        $this->action = $action;
    }
    public function layout($internalTemplateEngine)
    {
        $internalTemplateEngine->layout($this->getTemplate(), $this->data()->toArray());
    }
    public function getTemplate()
    {
        return ScheduledActions::TEMPLATE_BASE_PATH . "/layouts/actionContainer";
    }
    public function setActionPriority($priority) : \self
    {
        return $this->with(["actionPriority" => $priority]);
    }
    public function setActionName($actionName) : \self
    {
        return $this->with(["actionName" => $actionName]);
    }
    public function init()
    {
        return parent::init()->setActionName($this->action::$name)->setActionPriority((new \WHMCS\Support\Ticket\Actions\ActionsList())->getActionPriority($this->action));
    }
}

?>