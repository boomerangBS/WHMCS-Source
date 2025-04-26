<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

class TransferDomain extends AbstractCommand
{
    protected $command = "TransferDomain";
    const TRANSFER_REQUEST = "REQUEST";
    const TRANSFER_APPROVE = "APPROVE";
    const TRANSFER_DENY = "DENY";
    const TRANSFER_CANCEL = "CANCEL";
    const TRANSFER_USERTRANSFER = "USERTRANSFER";
    const TRANSFER_PUSH = "PUSH";
    const TRANSFER_TRADE = "TRADE";
    const TRANSFER_STATUSES = NULL;
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api, string $type, string $domain)
    {
        if(!in_array($type, self::TRANSFER_STATUSES)) {
            throw new \Exception("Invalid Domain Transfer Request type.");
        }
        $this->setParam("action", $type);
        $this->setParam("domain", $domain);
        parent::__construct($api);
    }
    public function setPeriod($years) : \self
    {
        $this->setParam("period", $years);
        return $this;
    }
    public function setEppCode($epp) : \self
    {
        $this->setParam("auth", $epp);
        return $this;
    }
    public function suppressContactTransferError($suppress) : \self
    {
        $this->setParam("FORCEREQUEST", (int) $suppress);
        return $this;
    }
    public function transferLock($lock) : \self
    {
        $this->setParam("TRANSFERLOCK", (int) $lock);
        return $this;
    }
    public function setOwnerContact($handle) : \self
    {
        $this->setParam("ownercontact0", $handle);
        return $this;
    }
    public function setAdminContact($handle) : \self
    {
        $this->setParam("admincontact0", $handle);
        return $this;
    }
    public function setBillingContact($handle) : \self
    {
        $this->setParam("billingcontact0", $handle);
        return $this;
    }
    public function setTechContact($handle) : \self
    {
        $this->setParam("techcontact0", $handle);
        return $this;
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
        $this->setParam("x-fee-amount0", $amount);
        return $this;
    }
}

?>