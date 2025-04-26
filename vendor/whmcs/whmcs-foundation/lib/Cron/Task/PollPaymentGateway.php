<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cron\Task;

class PollPaymentGateway extends \WHMCS\Scheduling\Task\AbstractTask
{
    protected $defaultPriority = 1575;
    protected $defaultDescription = "Runs the poll function for any active payment gateways. Runs before Automation Suspensions in case required.";
    protected $defaultName = "Poll Payment Gateways";
    protected $systemName = "PollPaymentGateway";
    public function __invoke()
    {
        $paymentGateways = new \WHMCS\Module\Gateway();
        $activeGateways = $paymentGateways->getActiveGateways();
        foreach ($activeGateways as $activeGateway) {
            $paymentGateways->load($activeGateway);
            if($paymentGateways->functionExists("poll")) {
                $paymentGateways->call("poll");
            }
        }
    }
}

?>