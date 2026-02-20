<?php

namespace WHMCS\Module\Registrar\CentralNic\Api;

interface TransportInterface
{
    public function doCall(\WHMCS\Module\Registrar\CentralNic\Commands\AbstractCommand $command, $api) : \WHMCS\Module\Registrar\CentralNic\Commands\AbstractCommand;
}

?>