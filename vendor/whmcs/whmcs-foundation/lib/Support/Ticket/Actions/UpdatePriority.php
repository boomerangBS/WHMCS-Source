<?php

namespace WHMCS\Support\Ticket\Actions;

class UpdatePriority extends AbstractAction
{
    use JSONParametersTrait;
    use OverlayParametersTrait;
    public $priority = "";
    public static $name = "UpdatePriority";
    public function execute()
    {
        if($this->priority === $this->ticket->getPriorityIdentifier()) {
            return true;
        }
        $ticket = new \WHMCS\Tickets();
        $ticket->setID($this->ticket->id);
        return $ticket->setPriority(\WHMCS\Support\Ticket::getPriorityAsLabel($this->priority));
    }
    public function detailString()
    {
        $key = "status." . $this->priority;
        $label = \AdminLang::trans($key);
        if($label === $key) {
            $label = $this->priority;
        }
        return $label;
    }
}

?>