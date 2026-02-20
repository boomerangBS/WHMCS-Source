<?php

namespace WHMCS\Service\Observers;

class ServiceHookObserver
{
    public function deleted(\WHMCS\Service\Service $service) : void
    {
        \HookMgr::run("ServiceDelete", ["userid" => $service->clientId, "clientId" => $service->clientId, "serviceid" => $service->id]);
    }
}

?>