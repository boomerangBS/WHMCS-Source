<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Billing\Payment;

class Transaction extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblaccounts";
    protected $dates = ["date"];
    protected $appends = ["formattedTransactionId"];
    protected $columnMap = ["clientId" => "userid", "currencyId" => "currency", "paymentGateway" => "gateway", "exchangeRate" => "rate", "transactionId" => "transid", "amountIn" => "amountin", "amountOut" => "amountout", "invoiceId" => "invoiceid", "refundId" => "refundid"];
    public $timestamps = false;
    public function client()
    {
        return $this->belongsTo("WHMCS\\User\\Client", "userid", "id", "client");
    }
    public function invoice()
    {
        return $this->belongsTo("WHMCS\\Billing\\Invoice", "invoiceid", "id", "invoice");
    }
    public function refunds() : \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany("WHMCS\\Billing\\Payment\\Transaction", "refundid");
    }
    public function originalTransaction() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo("WHMCS\\Billing\\Payment\\Transaction", "refundid", "id", "originalTransaction");
    }
    public function scopeLookup($query, $gateway, $transactionId)
    {
        return $query->where("gateway", $gateway)->where("transid", $transactionId);
    }
    public function gateway() : \WHMCS\Module\Gateway
    {
        if(empty($this->paymentGateway)) {
            return NULL;
        }
        return \WHMCS\Module\Gateway::factory($this->paymentGateway);
    }
    public function gatewaySafe() : \WHMCS\Module\Gateway
    {
        $gateway = NULL;
        try {
            $gateway = $this->gateway();
        } catch (\Exception $e) {
        }
        return $gateway;
    }
    public function gatewaySupports($feature)
    {
        $gateway = $this->gatewaySafe();
        if($gateway instanceof \WHMCS\Module\Gateway) {
            return $gateway->functionExists($feature);
        }
        return false;
    }
    public function gatewayCallIfSupports($default, string $feature, array $params = [])
    {
        $gateway = $this->gatewaySafe();
        if($gateway instanceof \WHMCS\Module\Gateway && $gateway->functionExists($feature)) {
            return $gateway->call($feature, $params);
        }
        return $default;
    }
    public function getFormattedTransactionIdAttribute()
    {
        $config = \DI::make("config");
        if($config->isTransactionFormattingEnabled()) {
            return $this->gatewayCallIfSupports($this->transactionId, "formatTransactionIdForDisplay", ["transactionId" => $this->transactionId]);
        }
        return $this->transactionId;
    }
    public function getTransactionIdMarkup()
    {
        if(!$this->gatewaySupports("TransactionInformation")) {
            return $this->formattedTransactionId ?? "";
        }
        return $this->getLink();
    }
    public function getLink()
    {
        $titleString = \AdminLang::trans("transactions.information.title");
        $infoString = \AdminLang::trans("transactions.information.tooltip");
        $uri = routePath("admin-billing-transaction-information", $this->id);
        return "<a href=\"" . $uri . "\" class=\"open-modal\" data-modal-title=\"" . $titleString . "\">\n    " . $this->formattedTransactionId . "\n    <i data-toggle=\"tooltip\"\n       data-container=\"body\"\n       data-placement=\"right auto\"\n       data-trigger=\"hover\"\n       class=\"fal fa-info-circle\"\n       title=\"" . $infoString . "\"\n    ></i>\n</a>";
    }
    public static function isUnique($gateway, $transactionId)
    {
        return !Transaction::lookup($gateway, $transactionId)->exists();
    }
    public static function assertUnique($gateway, $transactionId) : void
    {
        if(!static::isUnique($gateway, $transactionId)) {
            throw new \WHMCS\Exception\Module\NotServicable("Transaction ID \"" . $transactionId . "\" already exists");
        }
    }
}

?>