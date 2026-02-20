<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class AddWebFwd extends AbstractCommand
{
    protected $command = "AddWebFwd";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain, string $hostName, string $target, string $type)
    {
        $this->setParam("source", $hostName == "@" ? $domain : $hostName . "." . $domain)->setParam("target", $target)->setParam("type", $type == "URL" ? "RD" : "MRD");
        parent::__construct($api);
    }
}

?>