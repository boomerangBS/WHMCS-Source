<?php

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