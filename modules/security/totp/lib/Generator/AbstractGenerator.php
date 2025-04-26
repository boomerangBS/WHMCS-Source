<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Security\Totp\Generator;

abstract class AbstractGenerator implements GeneratorInterface
{
    public abstract function formatHtmlFromAuthString($authString);
    public static abstract function hasDependenciesMet();
    public function size() : int
    {
        return 200;
    }
    public function generate($accountName, string $secret, string $issuer)
    {
        $content = $this->getOtpAuthString($accountName, $secret, $issuer);
        return $this->formatHtmlFromAuthString($content);
    }
    private function getOtpAuthString($accountName, string $secret, string $issuer)
    {
        if($accountName === "" || strpos($accountName, ":") !== false) {
            throw new \RuntimeException("Invalid name: " . $accountName);
        }
        if($secret === "") {
            throw new \RuntimeException("Invalid secret");
        }
        if($issuer === "" || strpos($issuer, ":") !== false) {
            throw new \RuntimeException("Invalid issuer: " . $issuer);
        }
        $otpAuthString = "otpauth://totp/%s:%s?secret=%s&issuer=%s";
        return rawurlencode(sprintf($otpAuthString, $issuer, $accountName, $secret, $issuer));
    }
}

?>