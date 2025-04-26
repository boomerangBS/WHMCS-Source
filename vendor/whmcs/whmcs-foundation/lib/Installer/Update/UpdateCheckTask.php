<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Installer\Update;

class UpdateCheckTask extends \WHMCS\Scheduling\Task\AbstractTask
{
    public $description = "WHMCS Update Check";
    protected $frequency = "0 */8 * * *";
    public function __construct()
    {
        parent::__construct();
        $this->preventOverlapping();
    }
    public function __invoke()
    {
        $this->getOutput()->debug("a debug message", ["PreviousCheck" => \WHMCS\Config\Setting::getValue("UpdatesLastChecked")]);
        $this->getOutput()->info("Fetching Update Info");
        $updater = new Updater();
        return $updater->updateRemoteComposerData();
    }
}

?>