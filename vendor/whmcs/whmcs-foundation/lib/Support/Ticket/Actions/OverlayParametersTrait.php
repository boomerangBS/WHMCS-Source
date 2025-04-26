<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\Actions;

trait OverlayParametersTrait
{
    public function init(\WHMCS\Support\Ticket $ticket, array $parameters)
    {
        $this->ticket = $ticket;
        AbstractAction::overlayMapOnObject($parameters, $this);
        return $this;
    }
    public function assertParameters(array $parameters)
    {
        foreach ($this->getPublicPropertyMap() as $p => $value) {
            if(!isset($parameters[$p])) {
                throw new \InvalidArgumentException("Parameter '" . $p . "' is missing");
            }
        }
        return $this;
    }
}

?>