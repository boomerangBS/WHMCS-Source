<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Scheduling\Jobs;

class AsyncJobRouteProvider implements \WHMCS\Route\Contracts\ProviderInterface
{
    use \WHMCS\Route\ProviderTrait;
    public function getRoutes()
    {
        return ["/job" => [["method" => ["POST"], "name" => "run-async-job", "path" => "/run/async/{jobId:\\d+}", "handle" => ["WHMCS\\Scheduling\\Jobs\\AsyncJobController", "runJob"]]]];
    }
}

?>