<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

abstract class AbstractController
{
    use ParametersTrait;
    protected $params = [];
    protected $zonesFilename;
    protected $zonesFilePath;
    public function __construct(array $params)
    {
        $this->params = $params;
    }
    public function getDomainName()
    {
        $domainName = "";
        if($this->getParamString("domain_punycode")) {
            $domainName = $this->getParamString("domain_punycode");
        } elseif($this->getParamString("domainname")) {
            $domainName = $this->getParamString("domainname");
        }
        return $domainName;
    }
    public function getDomainTld()
    {
        $tld = "";
        if($this->getParamString("tld_punycode")) {
            $tld = $this->getParamString("tld_punycode");
        } elseif($this->getParamString("tld")) {
            $tld = $this->getParamString("tld");
        }
        return $tld;
    }
    public function getLastTldSegment()
    {
        return Domain::parseLastSegment($this->getDomainTld());
    }
    public function getDomainSld()
    {
        $sld = "";
        if($this->getParamString("sld_punycode")) {
            $sld = $this->getParamString("sld_punycode");
        } elseif($this->getParamString("sld")) {
            $sld = $this->getParamString("sld");
        }
        return $sld;
    }
    public function setZonesFilename($name) : \self
    {
        $this->zonesFilename = $name;
        return $this;
    }
    public function setZonesFilepath($path) : \self
    {
        $this->zonesFilePath = $path;
        return $this;
    }
    public function getApi() : Api\AbstractApi
    {
        return $this->api;
    }
    public function registerDomain() : Api\Response
    {
        $domain = new Domain($this->getDomainName());
        $adminContactHandle = $this->addAdminContact();
        $contactHandle = $this->addContact();
        !$this->hasParam("TransferLock") or $transferLock = !$this->hasParam("TransferLock") || $this->isParamEnabled("TransferLock");
        $addDomain = (new Commands\AddDomain($this->getApi(), $domain->getName(), $this->getParamInt("regperiod"), $transferLock, $this->isParamEnabled("idprotection"), $contactHandle, $adminContactHandle, $adminContactHandle, $adminContactHandle))->setNameServers(...array_filter([$this->getParamString("ns1"), $this->getParamString("ns2"), $this->getParamString("ns3"), $this->getParamString("ns4"), $this->getParamString("ns5")]))->setRenewalMode($this->getParam("renewalmode", "DEFAULT"));
        if($this->isParamEnabled("premiumEnabled") && $this->hasParam("premiumCost")) {
            $addDomain->setPremiumAmount($this->getParamFloat("premiumCost"));
        }
        if($this->getParamString("idnLanguage") != "") {
            $languageTag = (new IdnLanguage($this->getApi()))->getTagFor($domain->getTld(), $this->getParamString("idnLanguage"));
            if(is_null($languageTag)) {
                throw new \Exception(\Lang::trans("idnLanguage.invalidSelectionForTld", [":tld" => $domain->getTld()]));
            }
            $addDomain->setIDNLanguageTag($languageTag);
        }
        $fields = new AdditionalFields($domain->getLastSegment(), $this->getParamString("countrycode"), $this->getParamArray("additionalfields"));
        $addDomain->addParams($fields->getFields());
        if($fields->isDomainApplication()) {
            $addDomainApplication = (new Commands\AddDomainApplication($this->api))->addParams($addDomain->getParams());
            return $addDomainApplication->execute();
        }
        return $addDomain->execute();
    }
    public function transferDomain() : Api\Response
    {
        $domain = new DomainZone($this->getApi(), $this->getDomainName());
        (new Commands\CheckDomainTransfer($this->getApi(), Commands\TransferDomain::TRANSFER_REQUEST, $domain->getName(), $this->getParamString("eppcode")))->execute();
        $zoneInfo = (new Zones($this->zonesFilePath, $this->zonesFilename))->load()->findZone($domain->getZone());
        if(!$zoneInfo) {
            throw new \Exception("Zone Not Found");
        }
        $transfer = (new Commands\TransferDomain($this->getApi(), Commands\TransferDomain::TRANSFER_REQUEST, $domain->getName()))->setNameServers(...array_filter([$this->getParamString("ns1"), $this->getParamString("ns2"), $this->getParamString("ns3"), $this->getParamString("ns4"), $this->getParamString("ns5")]))->transferLock(true)->suppressContactTransferError(true);
        if($zoneInfo->eppRequired()) {
            $transfer->setEppCode($this->getParamString("eppcode"));
        }
        if($zoneInfo->renewsOnTransfer()) {
            $transfer->setPeriod($this->getParamInt("regperiod"));
        }
        if($this->isParamEnabled("premiumEnabled") && $this->hasParam("premiumCost")) {
            $transfer->setPremiumAmount($this->getParamFloat("premiumCost"));
        }
        if($domain->supportContactOnTransfer($zoneInfo)) {
            $contactHandle = $this->addContact();
            if(!$domain->isAfnic()) {
                $transfer->setOwnerContact($contactHandle)->setBillingContact($contactHandle);
            }
            $transfer->setAdminContact($contactHandle)->setTechContact($contactHandle);
        }
        $fields = new AdditionalFields($domain->getLastSegment(), $this->getParamString("countrycode"), $this->getParamArray("additionalfields"), true);
        $transfer->addParams($fields->getFields());
        return $transfer->execute();
    }
    public function renewDomain() : Api\Response
    {
        $domain = new DomainZone($this->getApi(), $this->getDomainName());
        $zoneInfo = (new Zones($this->zonesFilePath, $this->zonesFilename))->load()->findZone($domain->getZone());
        if($zoneInfo && !$zoneInfo->supportsRenewals()) {
            (new Commands\SetDomainRenewalMode($this->getApi(), $domain->getName(), Commands\SetDomainRenewalMode::RENEW_ONCE))->execute();
        }
        $renew = new Commands\RenewDomain($this->getApi(), $domain->getName(), $this->getParamInt("regperiod"), $this->getParam("expiryDate")->setTimezone("UTC")->year);
        if($this->isParamEnabled("premiumEnabled") && $this->hasParam("premiumCost")) {
            $renew->setPremiumAmount($this->getParamFloat("premiumCost"));
        }
        return $renew->execute();
    }
    public function addContact($new)
    {
        $companyName = AdditionalFields::transformCompanyName($this->getParamString("companyname"), $this->getLastTldSegment(), $this->getParamArray("additionalfields"));
        $contact = new Contact($this->getParamString("firstname"), $this->getParamString("lastname"), $companyName, $this->getParamString("address1"), $this->getParamString("address2"), $this->getParamString("city"), $this->getParamString("state"), $this->getParamString("postcode"), $this->getParamString("country"), $this->getParamString("email"), $this->getParamString("fullphonenumber"), $this->getParamString("fullfaxnumber"));
        return $contact->asRegistrant()->assertValid()->create($this->getApi(), $new)->getHandle();
    }
    public function addAdminContact($new)
    {
        $companyName = AdditionalFields::transformCompanyName($this->getParamString("companyname"), $this->getLastTldSegment(), $this->getParamArray("additionalfields"));
        $contact = new Contact($this->getParamString("adminfirstname"), $this->getParamString("adminlastname"), $companyName, $this->getParamString("adminaddress1"), $this->getParamString("adminaddress2"), $this->getParamString("admincity"), $this->getParamString("adminstate"), $this->getParamString("adminpostcode"), $this->getParamString("admincountry"), $this->getParamString("adminemail"), $this->getParamString("adminfullphonenumber"), $this->getParamString("adminfullfaxnumber"));
        return $contact->asAdmin()->assertValid()->create($this->getApi(), $new)->getHandle();
    }
    protected function updateOrAddContactDetails(Contact $contact = false, $addAsNew) : Contact
    {
        try {
            return $contact->updateOrCreate($this->getApi(), $addAsNew)->getHandle();
        } catch (\Exception $e) {
            throw new \Exception("Unable to update contact.", $e->getCode(), $e);
        }
    }
    protected function doAddDNSZone(string $domain)
    {
        (new Commands\AddDNSZone($this->getApi(), $domain))->execute();
    }
    protected function doModifyDomain(string $domain, array $optParams)
    {
        $modifyDomain = new Commands\ModifyDomain($this->getApi(), $domain);
        foreach ($optParams as $key => $value) {
            $modifyDomain->setParam($key, $value);
        }
        $modifyDomain->execute();
    }
    public function doModifyDNSZone(string $domain, array $optParams)
    {
        $modifyDNSZone = (new Commands\ModifyDNSZone($this->getApi()))->setParam("dnszone", $domain);
        foreach ($optParams as $key => $value) {
            $modifyDNSZone->setParam($key, $value);
        }
        $modifyDNSZone->execute();
    }
    protected function doDeleteWebFwd($domain, string $hostname) : void
    {
        (new Commands\DeleteWebFwd($this->getApi(), $domain, $hostname))->execute();
    }
    protected function doAddWebFwd($domain, string $hostname, string $address, string $type) : void
    {
        (new Commands\AddWebFwd($this->getApi(), $domain, $hostname, $address, $type))->execute();
    }
    public function getDomainInfo() : \WHMCS\Domain\Registrar\Domain
    {
        $domainInfo = $this->getStatusDomain($this->getDomainName());
        $nameservers = [];
        foreach ($domainInfo->getDataValue("nameserver") as $index => $ns) {
            $i = $index + 1;
            $nameservers["ns" . $i] = $ns;
        }
        $registrarDomain = (new \WHMCS\Domain\Registrar\Domain())->setDomain($domainInfo->getDataValue("domain"))->setNameservers($nameservers)->setRegistrationStatus($domainInfo->getDataValue("status"))->setTransferLock((bool) $domainInfo->getDataValue("transferlock"))->setExpiryDate(\WHMCS\Carbon::parse($domainInfo->getDataValue("registrationexpirationdate")))->setIsIrtpEnabled(true)->setIrtpVerificationTriggerFields(["Registrant" => ["First Name", "Last Name", "Organisation Name", "Email"]]);
        $timeToSuspension = $domainInfo->getDataValue("x-time-to-suspension");
        if($timeToSuspension) {
            $registrarDomain->setPendingSuspension(true)->setDomainContactChangeExpiryDate(\WHMCS\Carbon::parse($timeToSuspension));
        }
        $ownerContactHandle = $domainInfo->getDataValue("ownercontact");
        $contactInfo = $this->getStatusContact($ownerContactHandle);
        $registrarDomain->setRegistrantEmailAddress($contactInfo->getDataValue("email"));
        if($contactInfo->getDataValue("verificationrequested") == 1 && $contactInfo->getDataValue("verified") == 0) {
            $registrarDomain->setDomainContactChangePending(true)->setPendingSuspension(true);
        }
        try {
            $mailForwarding = $this->getQueryMailFwdList($this->getDomainName());
            $registrarDomain->setEmailForwardingStatus(0 < $mailForwarding->getDataValue("total"));
        } catch (\Exception $e) {
            $registrarDomain->setEmailForwardingStatus(false);
        }
        try {
            $dnsZone = $this->getCheckDNSZone($this->getDomainName());
            $registrarDomain->setDnsManagementStatus($dnsZone->getCode() != 210);
        } catch (\Exception $e) {
            $registrarDomain->setDnsManagementStatus(false);
        }
        $registrarDomain->setIdProtectionStatus($domainInfo->getDataValue("xwhoisprivacy"));
        return $registrarDomain;
    }
    protected function getQueryDNSZoneRRList($domain) : Api\Response
    {
        return (new Commands\QueryDNSZoneRRList($this->getApi(), $domain))->execute();
    }
    protected function getQueryWebFwdList($domain, string $hostname) : Api\Response
    {
        return (new Commands\QueryWebFwdList($this->getApi(), $domain, $hostname))->execute();
    }
    public function getEmailForwarding() : array
    {
        return $this->getCurrentEmailForwardingRules($this->getDomainName());
    }
    public function saveEmailForwarding() : void
    {
        if(!$this->getDomainName()) {
            throw new \Exception("Invalid Domain");
        }
        $prefix = $this->getParamArray("prefix");
        $forwardTo = $this->getParamArray("forwardto");
        if(count($prefix) != count($forwardTo)) {
            throw new \Exception("Number of prefix does not match number of forwardto");
        }
        $currentList = [];
        $forwardingRules = $this->getCurrentEmailForwardingRules($this->getDomainName());
        foreach ($forwardingRules as $value) {
            $currentList[$value["prefix"]] = $value["forwardto"];
        }
        $newList = array_combine($prefix, $forwardTo);
        $diff = Commands\QueryMailFwdList::diff($this->getDomainName(), $currentList, $newList);
        $deleteErrors = Commands\DeleteMailFwd::deleteList($this->getApi(), $diff["deleting"]);
        if(!empty($deleteErrors)) {
            throw new \Exception(json_encode($deleteErrors));
        }
        $addErrors = Commands\AddMailFwd::addList($this->getApi(), $diff["adding"]);
        if(!empty($addErrors)) {
            throw new \Exception(json_encode($addErrors));
        }
    }
    protected function getStatusDomain($domain) : Api\Response
    {
        return (new Commands\StatusDomain($this->getApi(), $domain))->execute();
    }
    protected function getStatusContact($contactHandle) : Api\Response
    {
        return (new Commands\StatusContact($this->getApi(), $contactHandle))->execute();
    }
    protected function getCurrentEmailForwardingRules($domain) : array
    {
        $emailFwdList = $this->getQueryMailFwdList($domain);
        return Commands\QueryMailFwdList::getList($emailFwdList);
    }
    protected function getQueryMailFwdList($domain) : Api\Response
    {
        return (new Commands\QueryMailFwdList($this->getApi(), $domain))->execute();
    }
    protected function getCheckDNSZone($domain) : Api\Response
    {
        return (new Commands\CheckDNSZone($this->getApi(), $domain))->execute();
    }
    public function getContactDetails() : array
    {
        $domainInfo = $this->getStatusDomain($this->getDomainName());
        $contacts["Registrant"] = Contact::populate($this->getApi(), $domainInfo->getDataValue("ownercontact"))->toArray();
        if($domainInfo->getDataValue("admincontact")) {
            $contacts["Admin"] = Contact::populate($this->getApi(), $domainInfo->getDataValue("admincontact"))->toArray();
        }
        if($domainInfo->getDataValue("billingcontact")) {
            $contacts["Billing"] = Contact::populate($this->getApi(), $domainInfo->getDataValue("billingcontact"))->toArray();
        }
        if($domainInfo->getDataValue("techcontact")) {
            $contacts["Tech"] = Contact::populate($this->getApi(), $domainInfo->getDataValue("techcontact"))->toArray();
        }
        return $contacts;
    }
    public function saveContactDetails() : array
    {
        $domainInfo = new DomainZone($this->getApi(), $this->getDomainName());
        try {
            $statusDomain = $this->getStatusDomain($domainInfo->getName());
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
        $currentOwnerContactHandle = $statusDomain->getDataValue("ownercontact");
        $currentAdminContactHandle = $statusDomain->getDataValue("admincontact");
        $currentBillingContactHandle = $statusDomain->getDataValue("billingcontact");
        $currentTechContactHandle = $statusDomain->getDataValue("techcontact");
        $modifyDomain = new Commands\ModifyDomain($this->getApi(), $domainInfo->getName());
        try {
            $zoneInfo = (new Zones($this->zonesFilePath, $this->zonesFilename))->load()->findZone($domainInfo->getZone());
            if(!$zoneInfo) {
                throw new \Exception("Zone Not found");
            }
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
        $ownerContactHandle = $this->updateOrAddContactDetails(Contact::factoryFromContactDetail($this->getArrayValueArray("Registrant", $this->getParamArray("contactdetails")))->asRegistrant()->assertValid()->setHandle($currentOwnerContactHandle)->setUpdateAllow($zoneInfo->handleUpdatable()));
        if($ownerContactHandle) {
            $modifyDomain->setOwnerContact($ownerContactHandle);
        }
        if($currentOwnerContactHandle != $ownerContactHandle && $this->isParamEnabled("irtpOptOut")) {
            $modifyDomain->setTransferLock(false);
        }
        $adminContactHandle = $this->updateOrAddContactDetails(Contact::factoryFromContactDetail($this->getArrayValueArray("Admin", $this->getParamArray("contactdetails")))->asAdmin()->assertValid()->setHandle($currentAdminContactHandle)->setUpdateAllow($zoneInfo->handleUpdatable()));
        if($adminContactHandle) {
            $modifyDomain->setAdminContact($adminContactHandle);
        }
        $billingContactHandle = $this->updateOrAddContactDetails(Contact::factoryFromContactDetail($this->getArrayValueArray("Billing", $this->getParamArray("contactdetails")))->asBilling()->assertValid()->setHandle($currentBillingContactHandle)->setUpdateAllow($zoneInfo->handleUpdatable()));
        if($billingContactHandle) {
            $modifyDomain->setBillingContact($billingContactHandle);
        }
        $techContactHandle = $this->updateOrAddContactDetails(Contact::factoryFromContactDetail($this->getArrayValueArray("Tech", $this->getParamArray("contactdetails")))->asTech()->assertValid()->setHandle($currentTechContactHandle)->setUpdateAllow($zoneInfo->handleUpdatable()));
        if($techContactHandle) {
            $modifyDomain->setTechContact($techContactHandle);
        }
        try {
            if(0 < count($modifyDomain->getParams()) && $zoneInfo->needsTrade() && $modifyDomain->getParamString("ownercontact0")) {
                $trade = new Commands\TradeDomain($this->getApi(), $domainInfo->getName(), $ownerContactHandle);
                $trade = AdditionalFields::transformTradeDomain($domainInfo->getTld(), $trade, $this->getParamArray("additionalfields"));
                $trade->execute();
                $modifyDomain->deleteParam("ownercontact0");
            }
            $response = $modifyDomain->execute();
            $pendingRegistrantVerification = false;
            if($response->getDataValue("ownerchangestatus") == "REQUESTED") {
                $pendingRegistrantVerification = true;
            }
            return ["success" => true, "pending" => $pendingRegistrantVerification];
        } catch (\Exception $e) {
            return ["error" => $e->getMessage()];
        }
    }
    public function checkAvailability() : \WHMCS\Domains\DomainLookup\ResultsList
    {
        $resultsList = new \WHMCS\Domains\DomainLookup\ResultsList();
        $searchTerm = $this->getParamString("searchTerm");
        if($this->hasParam("isIdnDomain") && $this->getParamString("punyCodeSearchTerm")) {
            $searchTerm = $this->getParamString("punyCodeSearchTerm");
        }
        $searchTerm = strtolower($searchTerm);
        $tldToInclude = $this->getParamArray("tldsToInclude");
        foreach (array_chunk($tldToInclude, Commands\CheckDomain::MAX_TLD_COUNT) as $tlds) {
            $checkDomains = new Commands\CheckDomains($this->getApi());
            $searchResults = [];
            $i = 0;
            foreach ($tlds as $tld) {
                $searchResults[$i] = new \WHMCS\Domains\DomainLookup\SearchResult($searchTerm, $tld);
                $checkDomains->setParam("domain" . $i, $searchTerm . $tld)->setParam("x-fee-command" . $i, "create")->setParam("x-fee-domain" . $i, $searchTerm . $tld);
                $i++;
            }
            $apiResponse = $checkDomains->execute();
            foreach ($searchResults as $key => $searchResult) {
                $domainAmount = $apiResponse->getData()["x-fee-amount"][$key];
                $domainClass = $apiResponse->getData()["x-fee-class"][$key];
                $domainCurrency = $apiResponse->getData()["x-fee-currency"][$key];
                $domainStatus = $apiResponse->getData()["domaincheck"][$key];
                substr($domainStatus, 0, 3);
                switch (substr($domainStatus, 0, 3)) {
                    case 210:
                        $status = \WHMCS\Domains\DomainLookup\SearchResult::STATUS_NOT_REGISTERED;
                        break;
                    case 211:
                        $status = \WHMCS\Domains\DomainLookup\SearchResult::STATUS_REGISTERED;
                        break;
                    default:
                        $status = \WHMCS\Domains\DomainLookup\SearchResult::STATUS_TLD_NOT_SUPPORTED;
                        $searchResult->setStatus($status);
                        if($domainClass === "premium") {
                            $searchResult->setPremiumDomain(true)->setStatus($this->params["premiumEnabled"] ? $status : \WHMCS\Domains\DomainLookup\SearchResult::STATUS_RESERVED)->setPremiumCostPricing(["CurrencyCode" => $domainCurrency, "register" => $domainAmount]);
                        }
                        $resultsList->append($searchResult);
                }
            }
        }
        return $resultsList;
    }
    public function getDomainSuggestions() : \WHMCS\Domains\DomainLookup\ResultsList
    {
        $suggestionSettings = $this->getParamArray("suggestionSettings");
        $maxResults = (int) $this->getArrayValue("maxResults", $suggestionSettings, 1);
        $getNameSuggestion = (new Commands\GetNameSuggestion($this->getApi()))->setParam("name", $this->getParamString("searchTerm"))->setParam("show-unavailable", 0)->setParam("max-results", $maxResults);
        if($this->isEnabled($this->getArrayValueString("ipAddress", $suggestionSettings))) {
            $getNameSuggestion->setParam("ipaddress", \App::getRemoteIp());
        }
        $filterContent = $this->isEnabled($this->getArrayValueString("filterContent", $suggestionSettings));
        $getNameSuggestion->setParam("filter-content", $filterContent ? 0 : 1);
        $apiResponse = $getNameSuggestion->execute();
        $resultsList = new \WHMCS\Domains\DomainLookup\ResultsList();
        foreach ($apiResponse->getDataValue("name") as $key => $domain) {
            $domainParts = explode(".", $domain, 2);
            if($apiResponse->getDataValue("availability")[$key] == "available" && in_array($domainParts[1], $this->getParamArray("tldsToInclude"))) {
                $searchResult = (new \WHMCS\Domains\DomainLookup\SearchResult($domainParts[0], $domainParts[1]))->setStatus(\WHMCS\Domains\DomainLookup\SearchResult::STATUS_NOT_REGISTERED);
                $resultsList->append($searchResult);
            }
        }
        return $resultsList;
    }
    public function getNameservers() : array
    {
        $statusDomain = $this->getStatusDomain($this->getDomainName());
        $nameServers = [];
        foreach ($statusDomain->getDataValue("nameserver") as $key => $nameServer) {
            $nameServerKey = $key + 1;
            $nameServers["ns" . $nameServerKey] = $nameServer;
        }
        return $nameServers;
    }
    public function saveNameservers() : void
    {
        $modifyDomain = new Commands\ModifyDomain($this->getApi(), $this->getDomainName());
        for ($i = 1; $i <= 5; $i++) {
            $modifyDomain->setParam("nameserver" . $i, $this->getAsciiParam("ns" . $i));
        }
        $modifyDomain->execute();
    }
    public function getDNS() : array
    {
        $checkDNSZone = $this->getCheckDNSZone($this->getDomainName());
        if($checkDNSZone->getCode() == 210) {
            $bestNameservers = \WHMCS\Domain\Domain::findOrFail($this->params["domainid"])->getBestNameserversForNewOrder();
            $this->doAddDNSZone($this->getDomainName());
            $this->doModifyDomain($this->getDomainName(), ["nameserver1" => $bestNameservers[0], "nameserver2" => $bestNameservers[1], "nameserver3" => "", "nameserver4" => "", "nameserver5" => ""]);
        }
        $values = [];
        $names = [];
        $zone = $this->getQueryDNSZoneRRList($this->getDomainName());
        for ($i = 0; $i < $zone->getDataValue("count"); $i++) {
            $name = $zone->getData()["name"][$i] ?? "";
            $type = $zone->getData()["type"][$i] ?? "";
            $content = $zone->getData()["content"][$i] ?? "";
            $priority = $zone->getData()["prio"][$i] ?? "";
            $ttl = $zone->getData()["ttl"][$i] ?? "";
            if($zone->getData()["locked"][$i] == 1) {
                $queryWebFwdList = $this->getQueryWebFwdList($this->getDomainName(), $name);
                if(0 < $queryWebFwdList->getDataValue("total") && !in_array($name, $names)) {
                    $names[] = $name;
                    $values[] = ["hostname" => $name, "type" => $queryWebFwdList->getDataValue("type") == "rd" ? "URL" : "FRAME", "address" => $queryWebFwdList->getDataValue("target")];
                }
            } else {
                if($type == "MX") {
                    if($content == $priority) {
                    } elseif(substr($content, 0, strlen($priority)) === $priority) {
                        $content = substr($content, strlen($priority) + 1);
                    }
                }
                $values[] = ["hostname" => $name, "type" => $type, "address" => $content, "priority" => $priority, "ttl" => $ttl];
            }
        }
        return $values;
    }
    public function saveDNS() : void
    {
        $i = 0;
        $apiParams = [];
        $existingRecords = $this->getDNS();
        foreach ($existingRecords as $record) {
            if(in_array($record["type"], ["URL", "FRAME"])) {
                $this->doDeleteWebFwd($this->getDomainName(), $record["hostname"]);
            } else {
                $values = $record["hostname"] . " " . $record["ttl"] . " IN " . $record["type"] . " " . $record["address"];
                if($record["type"] == "NS") {
                    $values = $record["hostname"] . " " . $record["ttl"] . " " . $record["type"] . " " . $record["address"];
                }
                $apiParams["delrr" . $i] = $values;
                $i++;
            }
        }
        $zone = [];
        $mxeHosts = [];
        $ttl = "28800";
        foreach ($this->getParamArray("dnsrecords") as $record) {
            if(empty($record["address"])) {
            } else {
                if(empty($record["hostname"]) || $record["hostname"] == $this->getDomainName()) {
                    $record["hostname"] = "@";
                }
                switch ($record["type"]) {
                    case "URL":
                    case "FRAME":
                        $this->doAddWebFwd($this->getDomainName(), $record["hostname"], $record["address"], $record["type"]);
                        break;
                    case "MX":
                    case "SRV":
                        $zone[] = $record["hostname"] . " " . $ttl . " IN " . $record["type"] . " " . $record["priority"] . " " . $record["address"];
                        break;
                    case "MXE":
                        if(preg_match("/^(\\d+)\\.(\\d+)\\.(\\d+)\\.(\\d+)\$/", $record["address"], $m)) {
                            $mxeIpAddress = $record["address"];
                            $mxeHostName = "mxe-host-for-ip-" . $m[1] . "-" . $m[2] . "-" . $m[3] . "-" . $m[4];
                            $mxeHosts[$mxeIpAddress] = $mxeHostName;
                            $zone[] = $record["hostname"] . " " . $ttl . " IN MX " . $record["priority"] . " " . $mxeHostName . "." . $this->getDomainName();
                        }
                        break;
                    case "NS":
                        $zone[] = $record["hostname"] . " " . $ttl . " " . $record["type"] . " " . $record["address"];
                        break;
                    default:
                        $zone[] = $record["hostname"] . " " . $ttl . " IN " . $record["type"] . " " . $record["address"];
                }
            }
        }
        foreach ($mxeHosts as $mxeIpAddress => $mxeHostName) {
            $zone[] = $mxeHostName . " " . $ttl . " IN A " . $mxeIpAddress;
        }
        $i = 0;
        foreach ($zone as $record) {
            $apiParams["addrr" . $i] = $record;
            $i++;
        }
        $this->doModifyDNSZone($this->getDomainName(), $apiParams);
    }
    public function getRegistrarLock()
    {
        $statusDomain = $this->getStatusDomain($this->getDomainName());
        return $statusDomain->getDataValue("transferlock") ? "locked" : "unlocked";
    }
    public function saveRegistrarLock() : void
    {
        $lockEnabled = (int) ($this->getParamString("lockenabled") == "locked");
        (new Commands\ModifyDomain($this->getApi(), $this->getDomainName()))->setTransferLock($lockEnabled)->execute();
    }
    public function registerNameserver() : void
    {
        (new Commands\AddNameserver($this->getApi()))->setParam("nameserver", $this->getAsciiParam("nameserver"))->setParam("ipaddress0", $this->getParamString("ipaddress"))->execute();
    }
    public function modifyNameserver() : void
    {
        (new Commands\ModifyNameserver($this->getApi()))->setParam("nameserver", $this->getAsciiParam("nameserver"))->setParam("delipaddress0", $this->getParamString("currentipaddress"))->setParam("addipaddress0", $this->getParamString("newipaddress"))->execute();
    }
    public function deleteNameserver() : void
    {
        (new Commands\DeleteNameserver($this->getApi()))->setParam("nameserver", $this->getAsciiParam("nameserver"))->execute();
    }
    public function releaseDomain() : void
    {
        $pushDomain = new Commands\PushDomain($this->getApi(), $this->getParamString("domainname"));
        if($this->getParamString("transfertag")) {
            $pushDomain->setParam("target", $this->getParamString("transfertag"));
        }
        $pushDomain->execute();
    }
    public function resendIRTPVerificationEmail() : void
    {
        $domainInfo = $this->getDomainInfo();
        (new Commands\ResendNotification($this->getApi()))->setParam("type", "contactverification")->setParam("object", $domainInfo->getRegistrantEmailAddress())->execute();
    }
    public function requestDelete() : void
    {
        $domainDeleted = 0;
        $domainName = $this->getDomainName();
        if($this->getParamString("DeleteMode") === "ImmediateIfPossible") {
            try {
                (new Commands\DeleteDomain($this->getApi(), $domainName))->execute();
                $domainDeleted = 1;
            } catch (\Exception $e) {
            }
        }
        if(!$domainDeleted) {
            (new Commands\SetDomainRenewalMode($this->getApi(), $domainName, Commands\SetDomainRenewalMode::AUTO_DELETE))->execute();
        }
    }
    public function getEppCode()
    {
        $authCode = NULL;
        if(in_array($this->getDomainTld(), ["de", "be", "no", "eu"])) {
            $setAuthCode = (new Commands\SetAuthcode($this->getApi()))->setParam("domain", $this->getDomainName())->execute();
            $authCode = $setAuthCode->getDataValue("auth") ?? NULL;
        }
        if(!$authCode) {
            $statusDomain = (new Commands\StatusDomain($this->getApi(), $this->getDomainName()))->execute();
            $authCode = $statusDomain->getDataValue("auth") ?? NULL;
        }
        if($authCode) {
            return $authCode;
        }
        throw new \Exception("No auth info code found");
    }
    public function toggleIdProtection() : void
    {
        (new Commands\ModifyDomain($this->getApi(), $this->getDomainName()))->setParam("x-whoisprivacy", (int) $this->isParamEnabled("protectenable"))->execute();
    }
    public function syncDomain() : array
    {
        $statusDomain = $this->getStatusDomain($this->getDomainName());
        $expiryDate = \WHMCS\Carbon::parse($statusDomain->getDataValue("paiduntildate"));
        $workingKnowledgeOfIsActive = function ($statusDomain) {
            $stati = $statusDomain->getDataValue("status");
            if(is_string($stati)) {
                if(strcasecmp("active", $stati) === 0) {
                    return true;
                }
                return false;
            }
            if(!is_array($stati)) {
                return false;
            }
            $knownActiveStatus = ["ok", "active", "clientupdateprohibited", "servertransferprohibited", "clientdeleteprohibited", "clienttransferprohibited", "clientrenewprohibited", "clienthold", "clienttransferprohibited"];
            $stati = array_map("strtolower", $stati);
            foreach ($knownActiveStatus as $versionOfActive) {
                if(in_array($versionOfActive, $stati)) {
                    return true;
                }
            }
            return false;
        };
        return ["active" => $workingKnowledgeOfIsActive($statusDomain), "expired" => \WHMCS\Carbon::now()->gt($expiryDate), "expirydate" => $expiryDate->toDateString()];
    }
    public function getTldPricing() : \WHMCS\Results\ResultsList
    {
        $result = new \WHMCS\Results\ResultsList();
        $zonesPricing = (new TldsPricing($this->getApi()))->load();
        $zones = (new Zones($this->zonesFilePath, $this->zonesFilename))->load();
        $zonesPricing->getAll()->each(function ($item) use($result, $zones) {
            $zoneInfo = $zones->findOrCreate($this->getApi(), $item->zone());
            if($zoneInfo) {
                $importItem = (new \WHMCS\Domain\TopLevel\ImportItem())->setExtension($item->tld())->setYears($zoneInfo->periodYears())->setRegisterPrice($item->setup() + $item->annual())->setRenewPrice($item->annual())->setTransferPrice($item->transfer())->setCurrency($item->currency())->setEppRequired($zoneInfo->eppRequired());
                if(0 < $zoneInfo->graceDays()) {
                    $importItem->setGraceFeeDays($zoneInfo->graceDays())->setGraceFeePrice($item->annual());
                }
                if(0 < $zoneInfo->redemptionDays()) {
                    $importItem->setRedemptionFeeDays($zoneInfo->redemptionDays());
                    $importItem->setRedemptionFeePrice($item->restore());
                }
                $result->append($importItem);
            }
        });
        return $result;
    }
    public function transferSync() : array
    {
        $values = [];
        $domainName = $this->getDomainName();
        try {
            $statusDomain = $this->getStatusDomain($domainName);
            $values["completed"] = true;
        } catch (\Exception $e) {
            try {
                $statusDomainTransfer = (new Commands\StatusDomainTransfer($this->getApi(), $domainName))->execute();
                if($statusDomainTransfer->getDataValue("transferstatus") == "failed") {
                    $values["failed"] = true;
                    $values["reason"] = implode("\\n", $statusDomainTransfer->getDataValue("transferlog"));
                } else {
                    $values["completed"] = false;
                }
            } catch (\Exception $e) {
                $values["error"] = "StatusDomainTransfer: " . $e->getMessage();
            }
            return $values;
        }
        $values["expirydate"] = \WHMCS\Carbon::parse($statusDomain->getDataValue("paiduntildate"))->toDateString();
        $domain = new DomainZone($this->getApi(), $domainName);
        $zoneInfo = (new Zones($this->zonesFilePath, $this->zonesFilename))->load()->findZone($domain->getZone());
        if($zoneInfo->renewsOnTransfer()) {
            $values["nextduedate"] = $values["expirydate"];
            $values["nextinvoicedate"] = $values["expirydate"];
        }
        $modifyDomainParams = [];
        $modifyDomainParams["transferlock"] = 1;
        $domain->setNameServers(...array_filter([$this->getParamString("ns1"), $this->getParamString("ns2"), $this->getParamString("ns3"), $this->getParamString("ns4"), $this->getParamString("ns5")]));
        if(0 < count($domain->getNameServers())) {
            $existingNameservers = $statusDomain->getData()["nameserver"] ?? [];
            $orderNameservers = $domain->getNameServers();
            sort($existingNameservers);
            sort($orderNameservers);
            $diffNameservers = array_udiff($orderNameservers, $existingNameservers, "strcasecmp");
            if(0 < count($diffNameservers)) {
                $i = 0;
                foreach ($orderNameservers as $nameserver) {
                    if(empty($nameserver)) {
                    } else {
                        $modifyDomainParams["nameserver" . $i] = $nameserver;
                        $i++;
                    }
                }
            }
        }
        $ownerId = $statusDomain->getDataValue("ownercontact");
        if(empty($ownerId)) {
            $ownerId = $this->addContact();
            $modifyDomainParams["ownercontact0"] = $ownerId;
        }
        $adminContactId = $statusDomain->getDataValue("admincontact");
        if(empty($adminContactId)) {
            if($domain->getTld() == "it") {
                $adminContactId = $ownerId;
            } else {
                $adminContactId = $this->addAdminContact();
            }
            $modifyDomainParams["admincontact0"] = $adminContactId;
        }
        $billingContactId = $statusDomain->getDataValue("billingcontact");
        if(empty($billingContactId)) {
            $modifyDomainParams["billingcontact0"] = $adminContactId;
        }
        $techContactId = $statusDomain->getDataValue("techcontact");
        if(empty($techContactId)) {
            $modifyDomainParams["techcontact0"] = $adminContactId;
        }
        $this->doModifyDomain($domainName, $modifyDomainParams);
        return $values;
    }
    public function handleDnsSec() : array
    {
        $error = NULL;
        $dsData = [];
        $keyData = [];
        $updated = false;
        $domainDnsSec = new DomainDnsSec($this->getApi(), $this->getDomainName());
        try {
            if(\App::isInRequest("DNSSEC")) {
                $records = \App::getFromRequest("DNSSEC") ?? [];
                foreach ($records as $data) {
                    if(empty($data["pubKey"])) {
                    } else {
                        if($this->hasWhiteSpaces($data["pubKey"])) {
                            throw new \Exception(\Lang::trans("domainDnsSec.publicKeyNoSpace"));
                        }
                        $domainDnsSec->addDnsSecRecord(new KeyData($data["flag"], $data["protocol"], $data["alg"], $data["pubKey"]));
                    }
                }
                if(0 < $domainDnsSec->getKeyData()->count()) {
                    $domainDnsSec->save();
                } else {
                    $domainDnsSec->deleteAll();
                }
                $updated = true;
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
        } finally {
            $domainDnsSec->load();
            $domainDnsSec->getDsData()->each(function ($item) use($dsData) {
                $dsData[] = ["keyTag" => $item->getKeyTag(), "alg" => $item->getAlg(), "digestType" => $item->getDigestType(), "digest" => $item->getDigest()];
            });
            $domainDnsSec->getKeyData()->each(function ($item) use($keyData) {
                $keyData[] = ["flag" => $item->getFlag(), "protocol" => $item->getProtocol(), "alg" => $item->getAlg(), "pubKey" => $item->getPubKey()];
            });
        }
    }
    public function configValidate() : void
    {
        $statusAccount = new Commands\StatusAccount($this->getApi());
        $statusAccount->execute();
    }
    protected function hasWhiteSpaces($string)
    {
        return preg_match("/\\s/", $string);
    }
}

?>