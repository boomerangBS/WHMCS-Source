<?php

namespace WHMCS\Billing\Gateway\Contract;

interface IdentifierInterface
{
    public function systemIdentifier();
    public function canonicalDisplayName();
    public function displayName();
}

?>