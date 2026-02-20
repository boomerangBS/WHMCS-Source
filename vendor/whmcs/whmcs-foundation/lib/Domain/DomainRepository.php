<?php

namespace WHMCS\Domain;

class DomainRepository
{
    private $currencyRepository;
    const TOP_TLD_SELECTION_LIMIT = 30;
    public function __construct(\WHMCS\Product\CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }
    public function getActiveDomainsStatistic() : array
    {
        try {
            $result = $this->getTldStatisticsPerRegistrar();
            $result["summary"] = $this->getTotalDomainStatistics();
            return $result;
        } catch (\Throwable $e) {
            return [];
        }
    }
    private function getTldStatisticsPerRegistrar() : array
    {
        return \WHMCS\Database\Capsule::table("tbldomains", "domains")->select(["domains.registrar", "domains.registrationperiod as registration_period", \WHMCS\Database\Capsule::raw("substr(domains.domain, position('.' in domains.domain) + 1) as tld"), \WHMCS\Database\Capsule::raw("count(domains.id) as domains_count"), \WHMCS\Database\Capsule::raw("sum(domains.recurringamount / currencies.rate) as default_currency_revenue")])->join("tblclients as clients", "clients.id", "=", "domains.userid")->leftJoin("tblcurrencies as currencies", "currencies.id", "=", "clients.currency")->where("domains.status", Status::ACTIVE)->groupBy("domains.registrar", "tld")->orderByDesc("domains_count")->get()->map(function ($item) {
            $item->default_currency_revenue = (double) $item->default_currency_revenue;
            return $item;
        })->groupBy("registrar")->map(function (\Illuminate\Support\Collection $tlds) {
            $summary = ["count" => $tlds->sum("domains_count"), "revenue" => $this->currencyRepository->getMoneyAmountsPerCurrency($tlds->sum("default_currency_revenue"))];
            return $tlds->take(self::TOP_TLD_SELECTION_LIMIT)->mapWithKeys(function ($item) {
                return [$item->tld => ["count" => $item->domains_count, "revenue" => $this->currencyRepository->getMoneyAmountsPerCurrency($item->default_currency_revenue)]];
            })->prepend($summary, "summary");
        })->toArray();
    }
    private function getTotalDomainStatistics() : array
    {
        $domainStatisticsPerPeriod = \WHMCS\Database\Capsule::table("tbldomains")->select(["tbldomains.registrationperiod as registration_period", \WHMCS\Database\Capsule::raw("count(tbldomains.id) as domains_count"), \WHMCS\Database\Capsule::raw("sum(tbldomains.recurringamount / tblcurrencies.rate) as default_currency_revenue")])->join("tblclients", "tblclients.id", "=", "tbldomains.userid")->leftJoin("tblcurrencies", "tblcurrencies.id", "=", "tblclients.currency")->where("tbldomains.status", Status::ACTIVE)->groupBy("tbldomains.registrationperiod")->orderBy("tbldomains.registrationperiod")->get()->map(function ($item) {
            $item->default_currency_revenue = (double) $item->default_currency_revenue;
            return $item;
        });
        $defaultCurrencyTotalRevenue = $domainStatisticsPerPeriod->sum("default_currency_revenue");
        $totalRevenuesPerCurrency = $this->currencyRepository->getMoneyAmountsPerCurrency($defaultCurrencyTotalRevenue);
        $periodStatistics = $domainStatisticsPerPeriod->mapWithKeys(function ($item) {
            return [$item->registration_period => ["count" => $item->domains_count, "revenue" => $this->currencyRepository->getMoneyAmountsPerCurrency($item->default_currency_revenue)]];
        });
        return ["count" => $domainStatisticsPerPeriod->sum("domains_count"), "revenue" => $totalRevenuesPerCurrency, "periods" => $periodStatistics->toArray()];
    }
}

?>