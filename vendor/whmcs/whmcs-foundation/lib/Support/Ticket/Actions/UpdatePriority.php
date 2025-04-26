<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
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