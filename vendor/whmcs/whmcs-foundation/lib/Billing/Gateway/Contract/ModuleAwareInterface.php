<?php

namespace WHMCS\Billing\Gateway\Contract;

interface ModuleAwareInterface
{
    public function getModule() : \WHMCS\Module\Gateway;
}

?>