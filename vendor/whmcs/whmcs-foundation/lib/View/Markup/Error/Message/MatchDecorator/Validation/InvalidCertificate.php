<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error\Message\MatchDecorator\Validation;

class InvalidCertificate extends \WHMCS\View\Markup\Error\Message\MatchDecorator\AbstractMatchDecorator
{
    use \WHMCS\View\Markup\Error\Message\MatchDecorator\GenericMatchDecorationTrait;
    const PATTERN_FAILED_CERT_LOAD = "/Invalid certificate content/";
    public function getTitle()
    {
        return "Certification Error - Invalid or Corrupt Certificate";
    }
    public function getHelpUrl()
    {
        return "https://go.whmcs.com/2389/troubleshooting-ssl";
    }
    protected function isKnown($data)
    {
        return preg_match(self::PATTERN_FAILED_CERT_LOAD, $data);
    }
}

?>