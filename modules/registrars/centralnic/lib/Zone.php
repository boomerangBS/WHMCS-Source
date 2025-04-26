<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class Zone
{
    protected $zone = "";
    protected $periods = "";
    protected $periodYears = [];
    protected $graceDays = 0;
    protected $redemptionDays = 0;
    protected $eppRequired = false;
    protected $idProtection = false;
    protected $supportsRenewals = false;
    protected $renewsOnTransfer = false;
    protected $handleUpdatable = false;
    protected $needsTrade = false;
    protected $updatedAt;
    const STALE_THRESHOLD = 30;
    public function __construct(string $zone, string $periods, int $graceDays, int $redemptionDays, $eppRequired, $idProtection, $supportsRenewals, $renewsOnTransfer, $handleUpdatable, $needsTrade, \Carbon\Carbon $updateAt)
    {
        $this->zone = $zone;
        $this->periods = $periods;
        $this->graceDays = $graceDays;
        $this->redemptionDays = $redemptionDays;
        $this->eppRequired = $eppRequired;
        $this->idProtection = $idProtection;
        $this->supportsRenewals = $supportsRenewals;
        $this->renewsOnTransfer = $renewsOnTransfer;
        $this->handleUpdatable = $handleUpdatable;
        $this->needsTrade = $needsTrade;
        $this->updatedAt = $updateAt;
        $this->periodStringToYears();
    }
    public function zone()
    {
        return $this->zone;
    }
    public function periods()
    {
        return $this->periods;
    }
    public function periodYears() : array
    {
        return $this->periodYears;
    }
    public function graceDays() : int
    {
        return $this->graceDays;
    }
    public function redemptionDays() : int
    {
        return $this->redemptionDays;
    }
    public function eppRequired()
    {
        return $this->eppRequired;
    }
    public function idProtection()
    {
        return $this->idProtection;
    }
    public function supportsRenewals()
    {
        return $this->supportsRenewals;
    }
    public function renewsOnTransfer()
    {
        return $this->renewsOnTransfer;
    }
    public function handleUpdatable()
    {
        return $this->handleUpdatable;
    }
    public function needsTrade()
    {
        return $this->needsTrade;
    }
    public function updatedAt() : \Carbon\Carbon
    {
        return $this->updatedAt;
    }
    public function isStale()
    {
        return self::STALE_THRESHOLD <= \Carbon\Carbon::now()->diffInDays($this->updatedAt());
    }
    public function toArray() : array
    {
        $eppRequired = (int) $this->eppRequired();
        $idProtection = (int) $this->idProtection();
        $supportsRenewals = (int) $this->supportsRenewals();
        $renewsOnTransfer = (int) $this->renewsOnTransfer();
        $handleUpdatable = (int) $this->handleUpdatable();
        $needsTrade = (int) $this->needsTrade();
        $updatedAt = $this->updatedAt()->format("Y-m-d H:i:s");
        return ["zone" => (string) $this->zone(), "periods" => (string) $this->periods(), "grace_days" => (string) $this->graceDays(), "redemption_days" => (string) $this->redemptionDays(), "epp_required" => (string) $eppRequired, "id_protection" => (string) $idProtection, "supports_renewals" => (string) $supportsRenewals, "renews_on_transfer" => (string) $renewsOnTransfer, "handle_updatable" => (string) $handleUpdatable, "needs_trade" => (string) $needsTrade, "updated_at" => (string) $updatedAt];
    }
    public function diff(Zone $zone) : array
    {
        return array_diff_assoc($zone->toArray(), $this->toArray());
    }
    public function diffExclude(Zone $zone, string $exclude) : array
    {
        $diff = $this->diff($zone);
        foreach ($exclude as $property) {
            unset($diff[$property]);
        }
        return $diff;
    }
    public function changed(Zone $zone) : array
    {
        return $this->diffExclude($zone, "updated_at");
    }
    protected function periodStringToYears() : void
    {
        preg_match_all("/(?:(\\d+)y)+/", $this->periods(), $matches);
        $this->periodYears = $matches[1];
    }
}

?>