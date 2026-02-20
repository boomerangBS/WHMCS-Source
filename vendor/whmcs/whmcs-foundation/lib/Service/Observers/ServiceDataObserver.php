<?php

namespace WHMCS\Service\Observers;

class ServiceDataObserver
{
    public function deleting(\WHMCS\Service\Service $service) : void
    {
        \WHMCS\Service\ServiceData::ofService($service)->delete();
    }
}

?>