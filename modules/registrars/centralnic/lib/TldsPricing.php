<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class TldsPricing
{
    protected $api;
    protected $list;
    public function __construct(Api\ApiInterface $api)
    {
        $this->api = $api;
    }
    public function load() : \self
    {
        if(empty($this->list)) {
            $this->list = collect();
            try {
                $response = (new Commands\QueryZoneList($this->api))->execute();
                foreach ($response->getData()["zone"] ?? [] as $id => $zone) {
                    if("YEAR" != $response->getData()["periodtype"][$id]) {
                    } elseif(empty($response->getData()["active"][$id])) {
                    } else {
                        $tlds = $this->extractTlds($zone, $response->getData()["3rds"][$id]);
                        foreach ($tlds as $index => $tld) {
                            $this->list->add(new TldPricing($tld, $zone, (double) $response->getData()["setup"][$id] ?? 0, (double) $response->getData()["annual"][$id] ?? 0, (double) $response->getData()["transfer"][$id] ?? 0, (double) $response->getData()["trade"][$id] ?? 0, (double) $response->getData()["restore"][$id] ?? 0, (double) $response->getData()["application"][$id] ?? 0, $response->getData()["currency"][$id] ?? "", (int) $response->getData()["domaincount"][$id] ?? 0));
                        }
                    }
                }
            } catch (\Exception $e) {
                throw new \Exception("Unable to retrieve zone from remote provider", $e->getCode(), $e);
            }
        }
        return $this;
    }
    public function getAll() : \Illuminate\Support\Collection
    {
        return $this->list;
    }
    public function findPricing($zone) : TldPricing
    {
        return $this->getAll()->first(function ($item) use($zone) {
            return $item->tld() == $zone;
        });
    }
    protected function extractTlds($zone, string $tldList) : array
    {
        $tlds = [];
        if(strpos($tldList, " ") !== false) {
            if(strpos($tldList, ",") !== false) {
                $tlds = explode(", ", $tldList);
            } elseif(preg_match("/[a-z\\.]/", $zone)) {
                $tlds[] = $zone;
            }
        } else {
            $tlds[] = $tldList;
        }
        return $tlds;
    }
}

?>