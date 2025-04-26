<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Hook;

class PublicRegistry
{
    private $manager;
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }
    public function add($hookName, $priority, $hookFunction = "", $rollbackFunction) : void
    {
        $this->manager->add($hookName, $priority, $hookFunction, $rollbackFunction);
    }
    public function log($hookName, $msg, ...$inputs)
    {
        $this->manager->log($hookName, $msg, ...$inputs);
    }
}

?>