<?php

namespace WHMCS\Module\Security\Totp\Generator;

interface GeneratorInterface
{
    public static function hasDependenciesMet();
    public function generate($accountName, string $secret, string $issuer);
    public function size() : int;
}

?>