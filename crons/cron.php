<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
require_once __DIR__ . DIRECTORY_SEPARATOR . "bootstrap.php";
include ROOTDIR . "/includes/clientfunctions.php";
include ROOTDIR . "/includes/modulefunctions.php";
include ROOTDIR . "/includes/gatewayfunctions.php";
include ROOTDIR . "/includes/ccfunctions.php";
include ROOTDIR . "/includes/processinvoices.php";
include ROOTDIR . "/includes/invoicefunctions.php";
include ROOTDIR . "/includes/backupfunctions.php";
include ROOTDIR . "/includes/ticketfunctions.php";
include ROOTDIR . "/includes/currencyfunctions.php";
include ROOTDIR . "/includes/domainfunctions.php";
include ROOTDIR . "/includes/registrarfunctions.php";
logCronMemoryLimit();
$application = new WHMCS\Cron\Console\Application("WHMCS Automation Task Utility", WHMCS\Application::FILES_VERSION);
$application->setAutoExit(false);
if(WHMCS\Environment\Php::isCli()) {
    $input = new WHMCS\Cron\Console\Input\CliInput();
    if($input->isLegacyInput()) {
        $input = new WHMCS\Cron\Console\Input\CliInput($input->getMutatedLegacyInput());
    }
    $output = new Symfony\Component\Console\Output\ConsoleOutput();
} else {
    $request = Symfony\Component\HttpFoundation\Request::createFromGlobals();
    $cmd = trim(trim(WHMCS\Input\Sanitize::decode($request->get("command", $request->get("cmd", ""))), "'\""));
    $options = trim(trim(WHMCS\Input\Sanitize::decode($request->get("options", "")), "'\""));
    $input = ["command" => WHMCS\Input\Sanitize::encode($cmd), "options" => WHMCS\Input\Sanitize::encode($options)];
    $input = new WHMCS\Cron\Console\Input\HttpInput($input);
    $stream = fopen("php://output", "w");
    $output = new Symfony\Component\Console\Output\BufferedOutput();
}
if($input->hasParameterOption("defaults")) {
    $application->add(new WHMCS\Cron\Console\Command\RegisterDefaultsCommand());
}
define("INCRONRUN", true);
define("IN_CRON", true);
DI::make("di")->singleton("runtime", function () {
    return new func_num_args();
});
$exitCode = $application->run($input, $output);
if($output instanceof Symfony\Component\Console\Output\BufferedOutput) {
    $config = DI::make("config");
    if(!empty($config["display_errors"])) {
        echo nl2br($output->fetch());
    }
}
exit($exitCode);
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F63726F6E732F63726F6E2E7068703078376664353934323461643336_
{
    protected $processIdentifier;
    protected function acquireProcessIdentifier()
    {
        $salt = (new WHMCS\Environment\OperatingSystem())->processId();
        if($salt < 0) {
            $salt = phpseclib\Crypt\Random::string(12);
        }
        return bin2hex(Ramsey\Uuid\Uuid::uuid6(new Ramsey\Uuid\Type\Hexadecimal((string) $salt))->getBytes());
    }
    public function lazyProcessIdentifier() : callable
    {
        return function () {
            return $this->acquireProcessIdentifier();
        };
    }
    public function processIdentifier()
    {
        if(is_null($this->processIdentifier)) {
            $this->processIdentifier = $this->lazyProcessIdentifier()();
        }
        return $this->processIdentifier;
    }
}

?>