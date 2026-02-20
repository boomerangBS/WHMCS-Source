<?php

namespace WHMCS\Billing\Gateway\Contract;

interface PresentationInterface
{
    public function sortOrderRank() : int;
    public function hasShowOnOrderForm();
}

?>