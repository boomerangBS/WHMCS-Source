<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class CheckDomainTransfer extends AbstractCommand
{
    protected $command = "CheckDomainTransfer";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $type, string $domain, string $eppCode)
    {
        if(!in_array($type, TransferDomain::TRANSFER_STATUSES)) {
            throw new \Exception("Invalid Check Domain Transfer type.");
        }
        $this->setParam("action", $type);
        $this->setParam("domain", $domain);
        if(!empty($eppCode)) {
            $this->setParam("auth", $eppCode);
        }
        parent::__construct($api);
    }
    public function handleResponse(\WHMCS\Module\Registrar\CentralNic\Api\Response $response) : \WHMCS\Module\Registrar\CentralNic\Api\Response
    {
        $response->getCode();
        switch ($response->getCode()) {
            case "218":
                return $response;
                break;
            default:
                throw new \Exception($response->getDescription(), $response->getCode());
        }
    }
}

?>