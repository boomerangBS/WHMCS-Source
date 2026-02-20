<?php

namespace WHMCS\Billing\Gateway\Contract;

interface IntegrationInterface
{
    public function type();
    public function typeByDefinition();
}

?>