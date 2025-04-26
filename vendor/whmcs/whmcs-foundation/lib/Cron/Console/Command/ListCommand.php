<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Cron\Console\Command;

class ListCommand extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this->setName("list")->setDefinition($this->createDefinition())->setDescription("Lists commands")->setHelp("The <info>%command.name%</info> command lists all commands:\n\n  <info>php %command.full_name%</info>\n\nYou can also display the commands for a specific namespace:\n\n  <info>php %command.full_name% test</info>");
    }
    public function getNativeDefinition()
    {
        return $this->createDefinition();
    }
    protected function execute(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $helper = new \Symfony\Component\Console\Helper\DescriptorHelper();
        $helper->describe($output, $this->getApplication(), ["format" => "txt", "raw_text" => "", "namespace" => $input->getArgument("namespace")]);
        return 0;
    }
    private function createDefinition()
    {
        return new \Symfony\Component\Console\Input\InputDefinition([new \Symfony\Component\Console\Input\InputArgument("namespace", \Symfony\Component\Console\Input\InputArgument::OPTIONAL, "The namespace name")]);
    }
}

?>