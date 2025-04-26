<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error\Message\MatchDecorator\SystemRequirements;

class DiskQuotaExceeded extends \WHMCS\View\Markup\Error\Message\MatchDecorator\AbstractMatchDecorator
{
    use \WHMCS\View\Markup\Error\Message\MatchDecorator\GenericMatchDecorationTrait;
    const PATTERN_DISK_QUOTA_EXCEEDED = "/Disk quota exceeded/";
    public function getTitle()
    {
        return "Insufficient Disk Space";
    }
    public function getHelpUrl()
    {
        return "https://go.whmcs.com/1913/updating#requirements-for-automatic-updates";
    }
    protected function isKnown($data)
    {
        return preg_match(static::PATTERN_DISK_QUOTA_EXCEEDED, $data);
    }
}

?>