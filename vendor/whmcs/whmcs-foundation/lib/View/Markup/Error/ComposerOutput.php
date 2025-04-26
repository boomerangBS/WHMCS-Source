<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\View\Markup\Error;

class ComposerOutput extends AbstractConsoleOutput
{
    public static function filterComposerNoise($line)
    {
        $line = trim($line);
        $line = str_replace(": mysql_connect(): The mysql extension is deprecated and will be removed in the future: use mysqli or PDO instead", "", $line);
        if(strpos($line, "update [--") === false) {
            return $line;
        }
        return "";
    }
    public static function getComposerOutputStack($data)
    {
        $stack = new \SplStack();
        $messages = explode("\n", $data);
        foreach ($messages as $message) {
            if($message = static::filterComposerNoise($message)) {
                $stack->push($message);
            }
        }
        return $stack;
    }
    public function getIterator() : \Traversable
    {
        return static::getComposerOutputStack($this->getText());
    }
    protected function getMatchDecorators()
    {
        return [new Message\MatchDecorator\SystemRequirements\DiskQuotaExceeded(), new Message\MatchDecorator\SystemRequirements\FunctionDisabled(), new Message\MatchDecorator\Validation\InvalidCertificate(), new Message\MatchDecorator\NetworkIssue\FailedKeyserverFetch(), new Message\MatchDecorator\FilePermission\NotWritablePath(), new Message\MatchDecorator\FilePermission\ApplyUpdateDryRun(), new Message\MatchDecorator\FilePermission\CacheNotWritable(), new Message\MatchDecorator\FilePermission\PostCommandCopy()];
    }
}

?>