<?php

namespace WHMCS\View\Markup\Error\Message\MatchDecorator\FilePermission;

class CacheNotWritable extends \WHMCS\View\Markup\Error\Message\MatchDecorator\AbstractMatchDecorator
{
    use \WHMCS\View\Markup\Error\Message\MatchDecorator\GenericMatchDecorationTrait;
    protected $errorLevel = \WHMCS\View\Markup\Error\ErrorLevelInterface::WARNING;
    const PATTERN_DIRECTORY_CACHE_NOT_WRITABLE = "/cache directory ([^,].*), or directory is not writable/";
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
        return preg_match(self::PATTERN_DIRECTORY_CACHE_NOT_WRITABLE, $data);
    }
}

?>