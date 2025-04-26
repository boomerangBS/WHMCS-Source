<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer\Cli\Log;

class ProgressHandler extends \Monolog\Handler\AbstractProcessingHandler
{
    protected $progressBar;
    protected $output;
    public function getProgressBar()
    {
        return $this->progressBar;
    }
    public function setProgressBar($progressBar)
    {
        $this->progressBar = $progressBar;
        return $this;
    }
    public function getOutput()
    {
        return $this->output;
    }
    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }
    protected function write($record) : void
    {
        $message = $record["message"];
        if(strpos($message, "Applying Updates Done") === 0) {
            $this->getProgressBar()->advance(1, $record["message"]);
            $finished = false;
            while (empty($finished)) {
                try {
                    $this->getProgressBar()->advance(1, $record["message"]);
                } catch (\Exception $e) {
                    $finished = true;
                }
            }
        } elseif(strpos($message, "Applying Updates") === 0) {
            $this->getProgressBar()->advance(1, $record["message"]);
        }
    }
}

?>