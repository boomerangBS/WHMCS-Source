<?php

namespace WHMCS\Service\Observers;

class ServiceAddonDataObserver
{
    public function deleting(\WHMCS\Service\Addon $addon) : void
    {
        \WHMCS\Service\ServiceData::ofAddon($addon)->delete();
    }
}

?>