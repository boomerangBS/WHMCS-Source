<?php

namespace WHMCS\Mail\Incoming\Provider;

interface MailAuthProviderInterface
{
    public static function getSupportedAuthTypes() : array;
    public static function supportsLegacyMailProtocols();
}

?>