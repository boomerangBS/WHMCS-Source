<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
class Plesk_ExtensionCommand
{
    const COMMAND_STYLE_SUBNODE = "subnode";
    const COMMAND_STYLE_ATTR = "attr";
    protected function callExtension($extension, string $command, array $moduleParams, array $commandParams, string $commandStyle) : array
    {
        $params = array_merge($moduleParams, ["extension" => $extension, "command" => $command, "commandParams" => $commandParams]);
        switch ($commandStyle) {
            case "subnode":
                $operation = "callExtension";
                break;
            case "attr":
                $operation = "callExtensionAttr";
                $responseContainer = Plesk_Registry::getInstance()->manager->{$operation}($params);
                return (array) ($responseContainer->xpath("//" . $extension . "/" . $command)[0] ?? []);
                break;
            default:
                throw new WHMCS\Exception\Module\NotServicable("Invalid API command style");
        }
    }
    public function callWpToolkitCli($command, array $moduleParams, array $cliParams) : array
    {
        return $this->callExtension("wp-toolkit", $command, $moduleParams, $cliParams, "subnode");
    }
    public function callSitejet($command, array $moduleParams, array $commandParams) : array
    {
        return $this->callExtension("plesk-sitejet", $command, $moduleParams, $commandParams, "attr");
    }
}

?>