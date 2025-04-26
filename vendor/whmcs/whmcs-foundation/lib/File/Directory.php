<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\File;

class Directory
{
    protected $path = "";
    public function __construct($path)
    {
        $this->setPath($path);
    }
    protected function setPath($path)
    {
        $full_path = ROOTDIR . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR;
        if(!is_dir($full_path)) {
            throw new \WHMCS\Exception("Not a valid directory");
        }
        $this->path = $full_path;
    }
    protected function getPath()
    {
        return $this->path;
    }
    public function getSubdirectories()
    {
        $folders = [];
        $dh = opendir($this->getPath());
        while (false !== ($folder = readdir($dh))) {
            if($folder != "." && $folder != ".." && is_dir($this->getPath() . $folder)) {
                $folders[] = $folder;
            }
        }
        closedir($dh);
        sort($folders);
        return $folders;
    }
    public function listFiles()
    {
        $files = [];
        $dh = opendir($this->getPath());
        while (false !== ($file = readdir($dh))) {
            if(is_file($this->getPath() . $file)) {
                $files[] = $file;
            }
        }
        closedir($dh);
        sort($files);
        return $files;
    }
}

?>