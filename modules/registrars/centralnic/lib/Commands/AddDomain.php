<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class AddDomain extends AbstractCommand
{
    protected $command = "AddDomain";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain, int $period, $transferLock, $whoisPrivacy, string $ownerContact, string $adminContact, string $techContact, string $billingContact)
    {
        $this->setParam("domain", $domain)->setParam("period", $period)->setParam("transferlock", (int) $transferLock)->setParam("X-WHOISPRIVACY", (int) $whoisPrivacy)->setParam("ownercontact0", $ownerContact)->setParam("admincontact0", $adminContact)->setParam("techcontact0", $techContact)->setParam("billingcontact0", $billingContact);
        parent::__construct($api);
    }
    public function setNameServer($nameServer, int $index) : \self
    {
        $this->setParam("nameserver" . $index, $nameServer);
        return $this;
    }
    public function setNameServers(...$nameservers) : \self
    {
        foreach ($nameservers as $index => $ns) {
            $this->setNameServer($ns, $index);
        }
        return $this;
    }
    public function setPremiumAmount($amount) : \self
    {
        $this->setParam("x-fee-amount", $amount);
        return $this;
    }
    public function setIDNLanguageTag($languageTag) : \self
    {
        $this->setParam("X-IDN-LANGUAGE", $languageTag);
        return $this;
    }
    public function setRenewalMode($mode) : \self
    {
        $this->setParam("RENEWALMODE", $mode);
        return $this;
    }
    public function setTransferMode($mode) : \self
    {
        $this->setParam("TRANSFERMODE", $mode);
        return $this;
    }
}

?>