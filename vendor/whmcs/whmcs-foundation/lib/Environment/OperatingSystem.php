<?php

namespace WHMCS\Environment;

class OperatingSystem
{
    public static function isWindows($phpOs = PHP_OS)
    {
        return in_array($phpOs, ["Windows", "WIN32", "WINNT"]);
    }
    public function isOwnedByMe($path)
    {
        return fileowner($path) == Php::getUserRunningPhp();
    }
    public function isServerCloudLinux()
    {
        return strpos(php_uname("r"), "lve") !== false;
    }
    public function processId() : int
    {
        $pid = -1;
        if(function_exists("posix_getpid")) {
            $pid = posix_getpid();
        } else {
            $pid = getmypid();
            if($pid === false) {
                $pid = -1;
            }
        }
        return $pid;
    }
}

?>