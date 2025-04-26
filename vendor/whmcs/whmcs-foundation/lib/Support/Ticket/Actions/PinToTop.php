<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\Actions;

class PinToTop extends AbstractAction
{
    use JSONParametersTrait;
    public $pinToTop;
    public static $name = "PinToTop";
    const STATE_UNPIN = 0;
    const STATE_PIN = 1;
    public function execute()
    {
        if($this->shouldPin() && !$this->ticket->isPinned()) {
            $this->ticket->pin();
        } elseif($this->ticket->isPinned()) {
            $this->ticket->unPin();
        }
        if(!$this->ticket->isDirty()) {
            return true;
        }
        if($this->ticket->save()) {
            $logger = $this->ticket->logger();
            $logger->withAction($this->detailString());
            $admin = $this->attributionAdmin();
            if(!is_null($admin)) {
                $logger->withAdminAttribution($admin);
            }
            $logger->log();
            return true;
        }
        return false;
    }
    public function shouldPin()
    {
        return $this->pinToTop === self::STATE_PIN;
    }
    public function shouldUnPin()
    {
        return $this->pinToTop === self::STATE_UNPIN;
    }
    public function detailString()
    {
        if($this->shouldPin()) {
            $state = \AdminLang::trans("support.ticket.pin");
        } else {
            $state = \AdminLang::trans("support.ticket.unpin");
        }
        return sprintf("%s %s", $state, \AdminLang::trans("mentions.entityTicket"));
    }
    public function init(\WHMCS\Support\Ticket $ticket, array $parameters)
    {
        $this->ticket = $ticket;
        $this->pinToTop = (int) $parameters["pinToTop"];
        return $this;
    }
    public function assertParameters(array $parameters)
    {
        if(!isset($parameters["pinToTop"]) || !is_numeric($parameters["pinToTop"])) {
            throw new \InvalidArgumentException("PinToTop parameter must be a number.");
        }
        return $this;
    }
    protected function getParametersToSerialize() : array
    {
        return ["pinToTop" => $this->pinToTop];
    }
}

?>