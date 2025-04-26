<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cron\Console\Command;

class SkipCommand extends AllCommand
{
    protected function configure()
    {
        parent::configure();
        $this->setName("skip")->setDescription("Execute specific automation tasks")->setHelp("This command will perform all automation tasks that are due to run at the time of script execution, except for those specified");
    }
    public function getInputBasedCollection(\Symfony\Component\Console\Input\InputInterface $input)
    {
        return $this->getHelper("task-collection")->getExcludeCollection($input);
    }
    protected function getSystemQueue()
    {
        $input = $this->io->getInput();
        $queue = parent::getSystemQueue();
        if($input->hasOption("DatabaseBackup") && $input->getOption("DatabaseBackup")) {
            foreach ($queue->keys() as $key) {
                if($queue->offsetGet($key) instanceof \WHMCS\Cron\Task\DatabaseBackup) {
                    $queue->offsetUnset($key);
                }
            }
        }
        return $queue;
    }
}

?>