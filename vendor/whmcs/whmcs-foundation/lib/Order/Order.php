<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Order;

class Order extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblorders";
    public $timestamps = false;
    protected $dates = ["date"];
    protected $columnMap = ["clientId" => "userid", "orderNumber" => "ordernum"];
    protected $appends = ["isPaid"];
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "userid", "id", "client");
    }
    public function contact()
    {
        return $this->belongsTo("WHMCS\\User\\Client\\Contact", "contactid", "id", "contact");
    }
    public function services()
    {
        return $this->hasMany("WHMCS\\Service\\Service", "orderid");
    }
    public function addons()
    {
        return $this->hasMany("WHMCS\\Service\\Addon", "orderid");
    }
    public function domains()
    {
        return $this->hasMany("WHMCS\\Domain\\Domain", "orderid");
    }
    public function invoice()
    {
        return $this->hasOne("WHMCS\\Billing\\Invoice", "id", "invoiceid");
    }
    public function invoiceItems() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany("WHMCS\\Billing\\Invoice\\Item", "invoiceid", "invoiceid");
    }
    public function promotion()
    {
        return $this->hasOne("WHMCS\\Product\\Promotion", "code", "promocode");
    }
    public function requestor()
    {
        return $this->belongsTo("WHMCS\\User\\User", "requestor_id", "id", "requestor");
    }
    public function upgrade()
    {
        return $this->hasOne("WHMCS\\Service\\Upgrade\\Upgrade", "orderid");
    }
    public function upgrades() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany("WHMCS\\Service\\Upgrade\\Upgrade", "orderid");
    }
    public function adminRequestor()
    {
        return $this->belongsTo("WHMCS\\User\\Admin", "admin_requestor_id", "id", "adminRequestor");
    }
    public function getOrderDataAttribute()
    {
        $orderData = $this->getRawAttribute("orderdata");
        if(!is_string($orderData) || strlen($orderData) == 0) {
            return NULL;
        }
        $data = json_decode($orderData, true);
        if(is_null($data) && json_last_error() !== JSON_ERROR_NONE) {
            $data = safe_unserialize($orderData);
        }
        return $data;
    }
    public function getRenewalsAttribute()
    {
        return static::unpackRawRenewals($this->getRawAttribute("renewals"));
    }
    public function setNewRenewalsAttribute($domains, array $services, array $addons) : \self
    {
        $this->setAttribute("renewals", static::packRawRenewals($domains, $services, $addons));
        return $this;
    }
    public static function unpackRawRenewals($rawRenewalsRawAttribute)
    {
        return (new static())->newRenewals()->unpack($rawRenewalsRawAttribute);
    }
    public static function packRawRenewals(array $domains, array $services, array $addons)
    {
        $newAttribute = (new static())->newRenewals();
        $newAttribute->domains = $domains;
        $newAttribute->services = $services;
        $newAttribute->addons = $addons;
        return $newAttribute->pack();
    }
    protected function newRenewals()
    {
        return new func_num_args();
    }
    public function getIsPaidAttribute()
    {
        if(0 < $this->invoiceId) {
            return $this->invoice->status == "Paid";
        }
        return false;
    }
    public function getNameservers()
    {
        return removeEmptyValues(arrayTrim(explode(",", $this->nameservers)));
    }
    public function getEppCodeByDomain($domain)
    {
        $eppCodes = safe_unserialize($this->transferSecret);
        if(is_array($eppCodes) && array_key_exists($domain, $eppCodes)) {
            return $eppCodes[$domain];
        }
        return NULL;
    }
    public static function add(int $clientId, string $orderNumber, string $paymentMethod, string $notes, int $contactId = 0, int $requestorId = 0, int $adminRequestorId = 0)
    {
        $order = new self();
        $order->userId = $clientId;
        $order->orderNumber = $orderNumber;
        $order->paymentMethod = $paymentMethod;
        $order->notes = $notes;
        $order->contactId = $contactId;
        $order->requestorId = $requestorId;
        $order->adminRequestorId = $adminRequestorId;
        $order->date = \WHMCS\Carbon::now();
        $order->status = \WHMCS\Utility\Status::PENDING;
        $order->ipAddress = \App::getRemoteIp();
        $order->save();
        logActivity("New Order Placed - Order ID: " . $order->id . " - User ID: " . $clientId, $clientId);
        return $order;
    }
    public function scopeDaysAgo(\Illuminate\Database\Eloquent\Builder $query, int $days) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereRaw("tblorders.date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)", [$days]);
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F4F726465722F4F726465722E7068703078376664353934323461316561_
{
    public $domains = [];
    public $services = [];
    public $addons = [];
    public function unpack($renewalsRawAttribute)
    {
        $data = json_decode($renewalsRawAttribute);
        if(!is_null($data) && json_last_error() === JSON_ERROR_NONE) {
            $unpacked = new self();
            $unpacked->domains = $data->domains;
            $unpacked->services = $data->services;
            $unpacked->addons = $data->addons;
            return $unpacked;
        }
        return $this;
    }
    public function pack()
    {
        $objectClassProperties = [];
        foreach (get_class_vars(get_class($this)) as $property => $value) {
            $objectClassProperties[$property] = $this->{$property};
        }
        return json_encode((object) $objectClassProperties);
    }
}

?>