<?php

namespace WHMCS\Payment\Event;

interface EventInterface
{
    public function make() : \self;
}

?>