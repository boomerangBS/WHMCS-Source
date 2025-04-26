<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error\Message\MatchDecorator\FilePermission;

class NotWritablePath extends \WHMCS\View\Markup\Error\Message\MatchDecorator\AbstractMatchDecorator
{
    use \WHMCS\View\Markup\Error\Message\MatchDecorator\GenericMatchDecorationTrait;
    const PATTERN_PATH_NOT_WRITABLE = "/Permission Error. Failed to create or modify path: (.*)/";
    const PATTERN_VENDOR_NOT_WRITABLE = "/(vendor(?:\\/whmcs(?:\\/whmcs)?)?) does not exist and could not be created/";
    const PATTERN_VENDOR_NO_DELETE = "/Could not delete (\\/(?:.*)vendor\\/(?:.*))/";
    const PATTERN_WHMCS_WHMCS_MISSING = "/file could not be written to ((?:.*)\\/vendor\\/whmcs\\/whmcs).*\\.zip: failed to open stream: No such file or directory/";
    const PATTERN_WHMCS_WHMCS_NOT_MUTABLE = "/file could not be written to ((?:.*)\\/vendor\\/whmcs\\/whmcs).*\\.zip: failed to open stream: Permission denied/";
    public function getTitle()
    {
        return "Insufficient File Permissions";
    }
    public function getHelpUrl()
    {
        return "https://go.whmcs.com/1913/updating#common-errors";
    }
    protected function isKnown($data)
    {
        return preg_match(static::PATTERN_PATH_NOT_WRITABLE, $data) || preg_match(static::PATTERN_VENDOR_NOT_WRITABLE, $data) || preg_match(static::PATTERN_VENDOR_NO_DELETE, $data) || preg_match(static::PATTERN_WHMCS_WHMCS_MISSING, $data) || preg_match(static::PATTERN_WHMCS_WHMCS_NOT_MUTABLE, $data);
    }
}

?>