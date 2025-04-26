<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\Actions;

class UpdateStatus extends AbstractAction
{
    use JSONParametersTrait;
    use OverlayParametersTrait;
    public $status = 0;
    public static $name = "UpdateStatus";
    public function execute()
    {
        if($this->status < 1) {
            throw new \InvalidArgumentException("Status can not be empty");
        }
        $status = \WHMCS\Support\Ticket\Status::find($this->status);
        if(is_null($status)) {
            throw new \InvalidArgumentException(sprintf("Unknown status ID \"%s\"", $this->status));
        }
        if($status->title === $this->getTicket()->status) {
            return true;
        }
        if(!function_exists("closeTicket")) {
            require ROOTDIR . "/includes/ticketfunctions.php";
        }
        if(!function_exists("getAdminName")) {
            require ROOTDIR . "/includes/adminfunctions.php";
        }
        if($this->status == \WHMCS\Support\Ticket\Status::where("title", \WHMCS\Support\Ticket\Status::STATUS_CLOSED)->first()->id) {
            return closeTicket($this->getTicket()->id, $this->attributeToAdminId);
        }
        $ticket = new \WHMCS\Tickets();
        $ticket->setID($this->getTicket()->id);
        return $ticket->setStatus($status->title);
    }
    public function detailString()
    {
        $status = \WHMCS\Support\Ticket\Status::find($this->status);
        if(is_null($status)) {
            return "";
        }
        return $status->adminTitle();
    }
}

?>