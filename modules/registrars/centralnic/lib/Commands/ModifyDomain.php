<?php

namespace WHMCS\Module\Registrar\CentralNic\Commands;

class ModifyDomain extends AbstractCommand
{
    protected $command = "ModifyDomain";
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $domain)
    {
        $this->setParam("domain", $domain);
        parent::__construct($api);
    }
    public function setOwnerContact($contactHandle) : \self
    {
        $this->setParam("ownercontact0", $contactHandle);
        return $this;
    }
    public function setAdminContact($contactHandle) : \self
    {
        $this->setParam("admincontact0", $contactHandle);
        return $this;
    }
    public function setBillingContact($contactHandle) : \self
    {
        $this->setParam("billingcontact0", $contactHandle);
        return $this;
    }
    public function setTechContact($contactHandle) : \self
    {
        $this->setParam("techcontact0", $contactHandle);
        return $this;
    }
    public function setTransferLock($lock) : \self
    {
        $this->setParam("transferlock", (int) $lock);
        return $this;
    }
}

?>