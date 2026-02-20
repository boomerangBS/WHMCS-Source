<?php

namespace WHMCS\Billing\Gateway\Contract;

interface LivecycleAwareInterface
{
    public function isObsolete();
    public function isSuperseded();
    public function getSupersedingSystemIdentifiers() : array;
    public function supersededBy() : \WHMCS\Billing\Gateway\Collection;
}

?>