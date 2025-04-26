<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment;

class PaymentGatewayProductMapping extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblpaymentgateways_product_mapping";
    public $timestamps = true;
    public function createTable($drop = false)
    {
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($drop) {
            $schemaBuilder->dropIfExists($this->getTable());
        }
        if(!$schemaBuilder->hasTable($this->getTable())) {
            $schemaBuilder->create($this->getTable(), function ($table) {
                $table->increments("id");
                $table->string("gateway")->default("");
                $table->string("account_identifier")->default("");
                $table->string("product_identifier")->default("");
                $table->string("remote_identifier")->default("");
                $table->timestamps();
            });
        }
    }
    public function scopeGateway($query, $gateway)
    {
        return $query->where("gateway", $gateway);
    }
    public function scopeAccountIdentifier($query, $accountIdentifier)
    {
        return $query->where("account_identifier", $accountIdentifier);
    }
    public function scopeProductIdentifier($query, $productIdentifier)
    {
        return $query->where("product_identifier", $productIdentifier);
    }
}

?>