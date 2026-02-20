<?php

if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
$query = WHMCS\Billing\Quote::query()->with("items")->with("client");
$filters = ["id" => "quoteid", "userid" => "userid", "subject" => "subject", "stage" => "stage", "datecreated" => "datecreated", "lastmodified" => "lastmodified", "validuntil" => "validuntil"];
foreach ($filters as $field => $parameter) {
    $value = App::getFromRequest($parameter);
    if(strlen($value) == 0) {
    } else {
        $query->where("tblquotes." . $field, "=", $value);
    }
}
unset($filters);
unset($field);
unset($parameter);
unset($value);
$apiresults = ["result" => "success", "totalresults" => 0, "startnumber" => 0, "numreturned" => 0, "quotes" => []];
$quoteTemplate = new func_num_args();
$clientTemplate = new func_num_args();
$lineItemTemplate = new func_num_args();
$apiresults["totalresults"] = $query->count();
$limitOffset = max((int) App::getFromRequest("limitstart"), 0);
$query->limit(ecoalesce((int) App::getFromRequest("limitnum"), 25));
$query->offset($limitOffset);
$apiresults["startnumber"] = $limitOffset;
$query->orderBy("id", "DESC");
$quotes = [];
foreach ($query->get() as $quote) {
    $quoteEntry = clone $quoteTemplate;
    $quoteEntry->id = $quote->id;
    $quoteEntry->subject = $quote->subject;
    $quoteEntry->stage = $quote->status;
    $quoteEntry->userid = $quote->clientId;
    $quoteEntry->firstname = $quote->firstName;
    $quoteEntry->lastname = $quote->lastName;
    $quoteEntry->companyname = $quote->companyName;
    $quoteEntry->email = $quote->email;
    $quoteEntry->address1 = $quote->address1;
    $quoteEntry->address2 = $quote->address2;
    $quoteEntry->city = $quote->city;
    $quoteEntry->state = $quote->state;
    $quoteEntry->postcode = $quote->postcode;
    $quoteEntry->country = $quote->country;
    $quoteEntry->phonenumber = $quote->phoneNumber;
    $quoteEntry->tax_id = $quote->taxId;
    $quoteEntry->currency = $quote->currency;
    $quoteEntry->subtotal = $quote->subtotal;
    $quoteEntry->tax1 = $quote->tax1;
    $quoteEntry->tax2 = $quote->tax2;
    $quoteEntry->total = $quote->total;
    $quoteEntry->proposal = $quote->proposal;
    $quoteEntry->customernotes = $quote->customerNotes;
    $quoteEntry->adminnotes = $quote->adminNotes;
    $quoteEntry->validuntil = $quote->getRawAttribute("validuntil");
    $quoteEntry->datecreated = $quote->getRawAttribute("datecreated");
    $quoteEntry->lastmodified = $quote->getRawAttribute("lastmodified");
    $quoteEntry->datesent = $quote->getRawAttribute("datesent");
    $quoteEntry->dateaccepted = $quote->getRawAttribute("dateaccepted");
    if(!is_null($quote->client)) {
        $quoteClient = clone $clientTemplate;
        $quoteClient->id = $quote->clientId;
        $quoteClient->firstname = $quote->client->firstName;
        $quoteClient->lastname = $quote->client->lastName;
        $quoteClient->companyname = $quote->client->companyName;
        $quoteClient->email = $quote->client->email;
        $quoteClient->datecreated = $quote->client->dateCreated;
        $quoteClient->groupid = $quote->client->groupId;
        $quoteClient->status = $quote->client->status;
        $quoteEntry->client = $quoteClient;
    }
    foreach ($quote->items as $lineItem) {
        $lineItemEntry = clone $lineItemTemplate;
        $lineItemEntry->id = $lineItem->id;
        $lineItemEntry->description = $lineItem->description;
        $lineItemEntry->quantity = $lineItem->quantity;
        $lineItemEntry->unitprice = $lineItem->unitPrice;
        $lineItemEntry->discount = $lineItem->discount;
        $lineItemEntry->taxable = (int) $lineItem->isTaxable;
        $quoteEntry->items[] = $lineItemEntry;
    }
    $quoteEntry->items = (object) ["item" => $quoteEntry->items];
    $quotes[] = $quoteEntry;
}
$apiresults["quotes"] =& $quotes;
$apiresults["numreturned"] = count($quotes);
unset($apiresults["quotes"]);
$apiresults["quotes"] = (object) ["quote" => $quotes];
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F696E636C756465732F6170692F67657471756F7465732E7068703078376664353934323439313835_
{
    public $id;
    public $subject;
    public $stage;
    public $validuntil;
    public $userid;
    public $client;
    public $firstname;
    public $lastname;
    public $companyname;
    public $email;
    public $address1;
    public $address2;
    public $city;
    public $state;
    public $postcode;
    public $country;
    public $phonenumber;
    public $tax_id;
    public $currency;
    public $subtotal;
    public $tax1;
    public $tax2;
    public $total;
    public $proposal;
    public $customernotes;
    public $adminnotes;
    public $datecreated;
    public $lastmodified;
    public $datesent;
    public $dateaccepted;
    public $items = [];
}
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F696E636C756465732F6170692F67657471756F7465732E7068703078376664353934323439323832_
{
    public $id;
    public $firstname;
    public $lastname;
    public $companyname;
    public $email;
    public $datecreated;
    public $groupid;
    public $status;
}
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F696E636C756465732F6170692F67657471756F7465732E7068703078376664353934323439333236_
{
    public $id;
    public $description;
    public $quantity;
    public $unitprice;
    public $discount;
    public $taxable;
}

?>