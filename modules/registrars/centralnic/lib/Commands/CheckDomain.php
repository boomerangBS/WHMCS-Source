<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class CheckDomain extends AbstractCommand
{
    protected $command = "CheckDomain";
    const MAX_TLD_COUNT = 32;
    public function handleResponse(\WHMCS\Module\Registrar\CentralNic\Api\Response $response) : \WHMCS\Module\Registrar\CentralNic\Api\Response
    {
        if(200 <= $response->getCode() && $response->getCode() <= 300) {
            return $response;
        }
        throw new \Exception($response->getDescription(), $response->getCode());
    }
}

?>