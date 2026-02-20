<?php

namespace WHMCS\Module\Contracts;

interface SenderModuleInterface
{
    public function settings();
    public function getName();
    public function getDisplayName();
    public function testConnection(array $params);
    public function send(array $params, \WHMCS\Mail\Message $message);
}

?>