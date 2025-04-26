<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error\Message\MatchDecorator\FilePermission;

class PostCommandCopy extends \WHMCS\View\Markup\Error\Message\MatchDecorator\AbstractMatchDecorator
{
    use \WHMCS\View\Markup\Error\Message\MatchDecorator\GenericMatchDecorationTrait;
    const PATTERN_DIRECTORY_UNABLE_TO_COPY = "/Unable to copy (.*) to (.*)/";
    const PATTERN_UNABLE_TO_PERFORM_EARLY_FILE_COPY = "/Failed to perform early file copy during WHMCS file relocation/";
    public function getTitle()
    {
        return "Insufficient File Permissions For Deployment";
    }
    public function getHelpUrl()
    {
        return "https://go.whmcs.com/1913/updating#common-errors";
    }
    protected function isKnown($data)
    {
        return preg_match(self::PATTERN_DIRECTORY_UNABLE_TO_COPY, $data) || preg_match(self::PATTERN_UNABLE_TO_PERFORM_EARLY_FILE_COPY, $data);
    }
}

?>