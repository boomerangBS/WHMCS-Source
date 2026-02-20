<?php

namespace WHMCS\Billing\Gateway\Contract;

interface SettingsAwareInterface
{
    public function getSettings() : \Illuminate\Support\Collection;
}

?>