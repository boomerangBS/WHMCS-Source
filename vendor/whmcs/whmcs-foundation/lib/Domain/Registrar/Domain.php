<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Domain\Registrar;

class Domain
{
    protected $domain;
    protected $expiryDate;
    protected $registrationStatus;
    protected $restorable;
    protected $renewBeforeExpiration;
    protected $idProtectionStatus;
    protected $dnsManagementStatus;
    protected $emailForwardingStatus;
    protected $nameservers = [];
    protected $transferLock;
    protected $transferLockExpiryDate;
    protected $irtpOptOutStatus;
    protected $irtpTransferLock;
    protected $irtpTransferLockExpiryDate;
    protected $domainContactChangePending;
    protected $domainContactChangeExpiryDate;
    protected $willDomainSuspend;
    protected $isIrtpEnabled;
    protected $irtpVerificationTriggerFields = [];
    protected $registrantEmailAddress;
    const STATUS_ACTIVE = "Active";
    const STATUS_ARCHIVED = "Archived";
    const STATUS_DELETED = "Deleted";
    const STATUS_EXPIRED = "Expired";
    const STATUS_INACTIVE = "InActive";
    const STATUS_SUSPENDED = "Suspended";
    const STATUS_PENDING_DELETE = "Pending Delete Restorable";
    public function getRegistrantEmailAddress()
    {
        return $this->registrantEmailAddress;
    }
    public function setRegistrantEmailAddress($registrantEmailAddress)
    {
        $this->registrantEmailAddress = $registrantEmailAddress;
        return $this;
    }
    public function getDomain()
    {
        return $this->domain;
    }
    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }
    public function setExpiryDate(\WHMCS\Carbon $expiryDate = NULL)
    {
        $this->expiryDate = $expiryDate;
        return $this;
    }
    public function getRegistrationStatus()
    {
        return $this->registrationStatus;
    }
    public function setRegistrationStatus($registrationStatus)
    {
        $this->registrationStatus = $registrationStatus;
        return $this;
    }
    public function getRestorable()
    {
        return $this->restorable;
    }
    public function setRestorable($restorable)
    {
        $this->restorable = (bool) $restorable;
        return $this;
    }
    public function getRenewBeforeExpiration()
    {
        return $this->renewBeforeExpiration;
    }
    public function setRenewBeforeExpiration($renewBeforeExpiration)
    {
        $this->renewBeforeExpiration = (bool) $renewBeforeExpiration;
        return $this;
    }
    public function getIdProtectionStatus()
    {
        return $this->idProtectionStatus;
    }
    public function setIdProtectionStatus($idProtectionStatus)
    {
        $this->idProtectionStatus = (bool) $idProtectionStatus;
        return $this;
    }
    public function getDnsManagementStatus()
    {
        return $this->dnsManagementStatus;
    }
    public function setDnsManagementStatus($dnsManagementStatus)
    {
        $this->dnsManagementStatus = (bool) $dnsManagementStatus;
        return $this;
    }
    public function getEmailForwardingStatus()
    {
        return $this->emailForwardingStatus;
    }
    public function setEmailForwardingStatus($emailForwardingStatus)
    {
        $this->emailForwardingStatus = (bool) $emailForwardingStatus;
        return $this;
    }
    public function hasNameservers()
    {
        return 0 < count($this->nameservers);
    }
    public function getNameservers()
    {
        return $this->nameservers;
    }
    public function setNameservers($nameservers)
    {
        $this->nameservers = $nameservers;
        return $this;
    }
    public function hasTransferLock()
    {
        return !is_null($this->transferLock);
    }
    public function getTransferLock()
    {
        return $this->transferLock;
    }
    public function setTransferLock($transferLock)
    {
        $this->transferLock = (bool) $transferLock;
        return $this;
    }
    public function getTransferLockExpiryDate()
    {
        return $this->transferLockExpiryDate;
    }
    public function setTransferLockExpiryDate(\WHMCS\Carbon $transferLockExpiryDate = NULL)
    {
        $this->transferLockExpiryDate = $transferLockExpiryDate;
        return $this;
    }
    public function getIrtpOptOutStatus()
    {
        return $this->irtpOptOutStatus;
    }
    public function setIrtpOptOutStatus($irtpOptOutStatus)
    {
        $this->irtpOptOutStatus = (bool) $irtpOptOutStatus;
        return $this;
    }
    public function getIrtpTransferLock()
    {
        return $this->irtpTransferLock;
    }
    public function setIrtpTransferLock($irtpTransferLock)
    {
        $this->irtpTransferLock = (bool) $irtpTransferLock;
        return $this;
    }
    public function getIrtpTransferLockExpiryDate()
    {
        return $this->irtpTransferLockExpiryDate;
    }
    public function setIrtpTransferLockExpiryDate(\WHMCS\Carbon $irtpTransferLockExpiryDate)
    {
        $this->irtpTransferLockExpiryDate = $irtpTransferLockExpiryDate;
        return $this;
    }
    public function isContactChangePending()
    {
        return $this->domainContactChangePending;
    }
    public function setDomainContactChangePending($domainContactChangePending)
    {
        $this->domainContactChangePending = (bool) $domainContactChangePending;
        return $this;
    }
    public function getDomainContactChangeExpiryDate()
    {
        return $this->domainContactChangeExpiryDate;
    }
    public function setDomainContactChangeExpiryDate(\WHMCS\Carbon $domainContactChangeExpiryDate = NULL)
    {
        $this->domainContactChangeExpiryDate = $domainContactChangeExpiryDate;
        return $this;
    }
    public function getPendingSuspension()
    {
        return $this->willDomainSuspend;
    }
    public function setPendingSuspension($willDomainSuspend)
    {
        $this->willDomainSuspend = (bool) $willDomainSuspend;
        return $this;
    }
    public function setIsIrtpEnabled($isIrtpEnabled)
    {
        $this->isIrtpEnabled = (bool) $isIrtpEnabled;
        return $this;
    }
    public function getIsIrtpEnabled()
    {
        return $this->isIrtpEnabled();
    }
    public function isIrtpEnabled()
    {
        return $this->isIrtpEnabled;
    }
    public function setIrtpVerificationTriggerFields(array $fields = [])
    {
        $this->irtpVerificationTriggerFields = $fields;
        return $this;
    }
    public function getIrtpVerificationTriggerFields()
    {
        return $this->irtpVerificationTriggerFields;
    }
}

?>