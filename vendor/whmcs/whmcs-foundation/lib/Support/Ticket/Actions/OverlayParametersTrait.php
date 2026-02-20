<?php

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