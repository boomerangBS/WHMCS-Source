<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment;

class SubscriptionObsolescenceJob implements \WHMCS\Scheduling\Contract\JobInterface
{
    use \WHMCS\Scheduling\Jobs\JobTrait;
    public function manageFromClientId($clientId, $obsoleteIdentifiers, $supersedingIdentifiers)
    {
        $client = \WHMCS\User\Client::find((int) $clientId);
        if(!$client) {
            return NULL;
        }
        if(!is_array($obsoleteIdentifiers) || !is_array($supersedingIdentifiers) || count($obsoleteIdentifiers) < 1 || count($supersedingIdentifiers) < 1) {
            return NULL;
        }
        $manager = SubscriptionObsolescence::factory($client, $obsoleteIdentifiers, $supersedingIdentifiers);
        $manager->manage();
    }
}

?>