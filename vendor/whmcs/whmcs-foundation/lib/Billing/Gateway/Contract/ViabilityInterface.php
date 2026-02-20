<?php

namespace WHMCS\Billing\Gateway\Contract;

interface ViabilityInterface
{
    public function isServiceable();
    public function isActive();
    public function isAvailable();
}

?>