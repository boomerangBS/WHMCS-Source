<?php

namespace WHMCS\Module\Registrar\CentralNic\Api;

interface ApiInterface
{
    public function call(\WHMCS\Module\Registrar\CentralNic\Commands\AbstractCommand $command) : Response;
}

?>