<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error\Message\MatchDecorator\FilePermission;

class ApplyUpdateDryRun extends \WHMCS\View\Markup\Error\Message\MatchDecorator\AbstractMatchDecorator
{
    const MESSAGE = "Apply update dry-run permission issues:";
    const PATH_DELIMITER = "|";
    public function getPatterns() : array
    {
        return [sprintf("/%s/", static::MESSAGE)];
    }
    public static function getPathMessage($issuePaths) : array
    {
        return sprintf("Apply update dry-run permission issues:%s", implode(static::PATH_DELIMITER, $issuePaths));
    }
    public static function getErrorMessage($issuePaths) : array
    {
        return sprintf("Apply update dry-run detected %d permission issues", count($issuePaths));
    }
    protected function extractIssuePaths(\Iterator $messages) : \Iterator
    {
        $messages->rewind();
        $compactPathSet = "";
        while ($messages->valid()) {
            if(strpos($messages->current(), static::MESSAGE) !== false) {
                $compactPathSet = $messages->current();
                break;
            }
            $messages->next();
        }
        $messages->rewind();
        return str_replace(static::MESSAGE, "", $compactPathSet);
    }
    public function issuePathsAsLines($paths)
    {
        return str_replace(static::PATH_DELIMITER, "\n", $paths);
    }
    public function __toString()
    {
        return sprintf("%s\n%s", static::MESSAGE, $this->issuePathsAsLines($this->extractIssuePaths($this->getParsedMessages())));
    }
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
        foreach ($this->getPatterns() as $pattern) {
            if(preg_match($pattern, $data) === 1) {
                return true;
            }
        }
        return false;
    }
    public function toHtml()
    {
        return $this->toGenericHtml($this->__toString());
    }
    public function toPlain()
    {
        return $this->toGenericPlain($this->__toString());
    }
}

?>