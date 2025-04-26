<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
if(!function_exists("addClient")) {
    require ROOTDIR . "/includes/clientfunctions.php";
}
if(!function_exists("getCartConfigOptions")) {
    require ROOTDIR . "/includes/configoptionsfunctions.php";
}
if(!function_exists("getTLDPriceList")) {
    require ROOTDIR . "/includes/domainfunctions.php";
}
if(!function_exists("updateInvoiceTotal")) {
    require ROOTDIR . "/includes/invoicefunctions.php";
}
if(!function_exists("createInvoices")) {
    require ROOTDIR . "/includes/processinvoices.php";
}
if(!function_exists("calcCartTotals")) {
    require ROOTDIR . "/includes/orderfunctions.php";
}
if(!function_exists("ModuleBuildParams")) {
    require ROOTDIR . "/includes/modulefunctions.php";
}
if(!empty($promocode) && empty($promooverride)) {
    define("CLIENTAREA", true);
}
$whmcs = WHMCS\Application::getInstance();
try {
    $client = WHMCS\User\Client::findOrFail($whmcs->get_req_var("clientid"));
    $blockedStatus = ["Closed"];
    if(in_array($client->status, $blockedStatus)) {
        $apiresults = ["result" => "error", "message" => "Unable to add order when client status is " . $client->status];
        return NULL;
    }
} catch (Exception $e) {
    $apiresults = ["result" => "error", "message" => "Client ID Not Found"];
    return NULL;
}
$userid = (int) $client->id;
$gatewayModules = WHMCS\Module\GatewaySetting::getActiveGatewayModules();
if(!in_array($paymentmethod, $gatewayModules)) {
    $apiresults = ["result" => "error", "message" => "Invalid Payment Method. Valid options include " . implode(",", $gatewayModules)];
} else {
    if(!empty($clientip)) {
        if(filter_var($clientip, FILTER_VALIDATE_IP) === false) {
            $apiresults = ["result" => "error", "message" => "Invalid IP address provided for 'clientip'"];
            return NULL;
        }
        global $remote_ip;
        $remote_ip = $clientip;
        WHMCS\Order\Order::creating(function ($model) use($clientip) {
            $model->setAttribute("ipAddress", $clientip);
        });
    }
    unset($clientip);
    global $currency;
    $currency = getCurrency($userid);
    $_SESSION["cart"] = [];
    $domain = $whmcs->getFromRequest("domain", NULL);
    $billingcycle = $whmcs->getFromRequest("billingcycle", NULL);
    $qty = $whmcs->getFromRequest("qty", NULL);
    $addonsqty = $whmcs->getFromRequest("addonsqty", NULL);
    $addons = $whmcs->getFromRequest("addons", NULL);
    if(isset($pid) && is_array($pid)) {
        !is_array($domain) or !is_array($domain) && ($domain = []);
        !is_array($billingcycle) && ($billingcycle = []);
        foreach ($pid as $i => $prodid) {
            if($prodid) {
                $proddomain = $domain[$i] ?? NULL;
                $prodbillingcycle = $billingcycle[$i] ?? NULL;
                $configoptionsarray = [];
                $customfieldsarray = [];
                $domainfieldsarray = [];
                $addonsarray = [];
                $addonList = isset($addons[$i]) ? explode(",", $addons[$i]) : [];
                $qtyList = isset($addonsqty[$i]) ? explode(",", $addonsqty[$i]) : [];
                if(!empty($addonList)) {
                    foreach ($addonList as $key => $addonForPid) {
                        $qtyForAddon = !empty($qtyList[$key]) ? $qtyList[$key] : 1;
                        $addonsarray[] = ["addonid" => $addonForPid, "qty" => $qtyForAddon];
                    }
                }
                if(isset($configoptions[$i]) && $configoptions[$i]) {
                    $configoptionsarray = safe_unserialize(base64_decode($configoptions[$i]));
                }
                if(isset($customfields[$i]) && $customfields[$i]) {
                    $customfieldsarray = safe_unserialize(base64_decode($customfields[$i]));
                }
                $productarray = ["pid" => $prodid, "qty" => $qty[$i] ?? 1, "domain" => $proddomain, "billingcycle" => $prodbillingcycle, "server" => "", "configoptions" => $configoptionsarray, "customfields" => $customfieldsarray, "addons" => $addonsarray, "strictDomain" => false];
                if(!empty($hostname[$i]) || !empty($ns1prefix[$i]) || !empty($ns2prefix[$i]) || !empty($rootpw[$i])) {
                    $productarray["server"] = ["hostname" => $hostname[$i] ?? NULL, "ns1prefix" => $ns1prefix[$i] ?? NULL, "ns2prefix" => $ns2prefix[$i] ?? NULL, "rootpw" => $rootpw[$i] ?? NULL];
                }
                if(isset($priceoverride[$i]) && strlen($priceoverride[$i])) {
                    $productarray["priceoverride"] = $priceoverride[$i];
                }
                $_SESSION["cart"]["products"][] = $productarray;
            }
        }
    } elseif(!empty($pid)) {
        $configoptionsarray = [];
        $customfieldsarray = [];
        $domainfieldsarray = [];
        $addonsarray = [];
        $addonList = isset($addons[0]) ? explode(",", $addons[0]) : [];
        $qtyList = isset($addonsqty[0]) ? explode(",", $addonsqty[0]) : [];
        if(!empty($addonList)) {
            foreach ($addonList as $key => $addonForPid) {
                $qtyForAddon = !empty($qtyList[$key]) ? $qtyList[$key] : 1;
                $addonsarray[] = ["addonid" => $addonForPid, "qty" => $qtyForAddon];
            }
        }
        if(isset($configoptions) && $configoptions) {
            $configoptions = base64_decode($configoptions);
            $configoptionsarray = safe_unserialize($configoptions);
        }
        if(isset($customfields) && $customfields) {
            $customfields = base64_decode($customfields);
            $customfieldsarray = safe_unserialize($customfields);
        }
        $productarray = ["pid" => $pid, "qty" => $qty, "domain" => $domain, "billingcycle" => $billingcycle, "server" => "", "configoptions" => $configoptionsarray, "customfields" => $customfieldsarray, "addons" => $addonsarray, "strictDomain" => false];
        if(!empty($hostname) || !empty($ns1prefix) || !empty($ns2prefix) || !empty($rootpw)) {
            $productarray["server"] = ["hostname" => $hostname ?? NULL, "ns1prefix" => $ns1prefix ?? NULL, "ns2prefix" => $ns2prefix ?? NULL, "rootpw" => $rootpw ?? NULL];
        }
        if(isset($priceoverride) && strlen($priceoverride)) {
            $productarray["priceoverride"] = $priceoverride;
        }
        $_SESSION["cart"]["products"][] = $productarray;
    }
    $requestOptionalArray = function ($requestVar) {
        $value = [];
        if(App::isInRequest($requestVar)) {
            $value = App::getFromRequest($requestVar);
            if(!is_array($value)) {
                throw new UnexpectedValueException($requestVar);
            }
        }
        return $value;
    };
    $domaintype = App::getFromRequest("domaintype");
    $domainfields = App::getFromRequest("domainfields");
    $domain = App::getFromRequest("domain");
    $regperiod = App::getFromRequest("regperiod");
    $idnLanguage = App::getFromRequest("idnlanguage");
    $dnsmanagement = App::getFromRequest("dnsmanagement");
    $emailforwarding = App::getFromRequest("emailforwarding");
    $idprotection = App::getFromRequest("idprotection");
    $eppcode = App::getFromRequest("eppcode");
    $domainpriceoverride = App::getFromRequest("domainpriceoverride");
    $domainrenewoverride = App::getFromRequest("domainrenewoverride");
    if(is_array($domaintype)) {
        try {
            $domainfields = $requestOptionalArray("domainfields");
            $domain = $requestOptionalArray("domain");
            $regperiod = $requestOptionalArray("regperiod");
            $idnLanguage = $requestOptionalArray("idnlanguage");
            $dnsmanagement = $requestOptionalArray("dnsmanagement");
            $emailforwarding = $requestOptionalArray("emailforwarding");
            $idprotection = $requestOptionalArray("idprotection");
            $eppcode = $requestOptionalArray("eppcode");
            $domainpriceoverride = $requestOptionalArray("domainpriceoverride");
            $domainrenewoverride = $requestOptionalArray("domainrenewoverride");
        } catch (UnexpectedValueException $e) {
            $apiresults = ["result" => "error", "message" => "Expecting parameter '" . $e->getMessage() . "' to be an array"];
            return NULL;
        }
        foreach ($domaintype as $i => $type) {
            if($type) {
                if(array_key_exists($i, $domainfields)) {
                    $domainfields[$i] = base64_decode($domainfields[$i]);
                    $domainfieldsarray[$i] = safe_unserialize($domainfields[$i]);
                } else {
                    $domainfields[$i] = NULL;
                    $domainfieldsarray[$i] = NULL;
                }
                $idnLanguage[$i] = $idnLanguage[$i] ?? "";
                $dnsmanagement[$i] = $dnsmanagement[$i] ?? "";
                $emailforwarding[$i] = $emailforwarding[$i] ?? "";
                $idprotection[$i] = $idprotection[$i] ?? "";
                $eppcode[$i] = $eppcode[$i] ?? "";
                $domainArray = ["type" => $type, "domain" => $domain[$i], "regperiod" => $regperiod[$i], "idnLanguage" => $idnLanguage[$i], "dnsmanagement" => $dnsmanagement[$i], "emailforwarding" => $emailforwarding[$i], "idprotection" => $idprotection[$i], "eppcode" => $eppcode[$i], "fields" => $domainfieldsarray[$i]];
                if(isset($domainpriceoverride[$i]) && 0 < strlen($domainpriceoverride[$i])) {
                    $domainArray["domainpriceoverride"] = $domainpriceoverride[$i];
                }
                if(isset($domainrenewoverride[$i]) && 0 < strlen($domainrenewoverride[$i])) {
                    $domainArray["domainrenewoverride"] = $domainrenewoverride[$i];
                }
                $_SESSION["cart"]["domains"][] = $domainArray;
            }
        }
    } elseif($domaintype) {
        if($domainfields) {
            $domainfields = base64_decode($domainfields);
            $domainfieldsarray = safe_unserialize($domainfields);
        }
        if(empty($idnLanguage)) {
            $idnLanguage = "";
        }
        $domainArray = ["type" => $domaintype, "domain" => $domain, "regperiod" => $regperiod, "idnLanguage" => $idnLanguage, "dnsmanagement" => $dnsmanagement, "emailforwarding" => $emailforwarding, "idprotection" => $idprotection, "eppcode" => $eppcode, "fields" => $domainfieldsarray ?? NULL];
        if(isset($domainpriceoverride) && 0 < strlen($domainpriceoverride)) {
            $domainArray["domainpriceoverride"] = $domainpriceoverride;
        }
        if(isset($domainrenewoverride) && 0 < strlen($domainrenewoverride)) {
            $domainArray["domainrenewoverride"] = $domainrenewoverride;
        }
        $_SESSION["cart"]["domains"][] = $domainArray;
    }
    $serviceid = $whmcs->getFromRequest("serviceid", NULL);
    $addonidqty = $whmcs->getFromRequest("addonidqty", NULL);
    if(isset($addonid) && $addonid) {
        $addonData = WHMCS\Database\Capsule::table("tbladdons")->find($addonid);
        if(!$addonData) {
            $apiresults = ["result" => "error", "message" => "Addon ID invalid"];
            return NULL;
        }
        $addonid = $addonData->id;
        $allowMultipleQuantities = (int) $addonData->allowqty;
        if($allowMultipleQuantities === 1) {
            $allowMultipleQuantities = 0;
        }
        $serviceid = get_query_val("tblhosting", "id", ["userid" => $userid, "id" => $serviceid]);
        if(!$serviceid) {
            $apiresults = ["result" => "error", "message" => "Service ID not owned by Client ID provided"];
            return NULL;
        }
        $_SESSION["cart"]["addons"][] = ["id" => $addonid, "productid" => $serviceid, "qty" => $allowMultipleQuantities === 2 && !empty($addonidqty) ? $addonidqty : 1, "allowsQuantity" => $allowMultipleQuantities ?? NULL];
    }
    $serviceids = $whmcs->getFromRequest("serviceids", NULL);
    $addonidsqty = $whmcs->getFromRequest("addonidsqty", NULL);
    if(isset($addonids) && is_array($addonids)) {
        foreach ($addonids as $i => $addonid) {
            $addonData = WHMCS\Database\Capsule::table("tbladdons")->find($addonid);
            if(!$addonData) {
                $apiresults = ["result" => "error", "message" => "Addon ID invalid"];
                return NULL;
            }
            $addonid = $addonData->id;
            $allowsQuantity = (int) $addonData->allowqty;
            if($allowsQuantity === 1) {
                $allowsQuantity = 0;
            }
            $serviceid = get_query_val("tblhosting", "id", ["userid" => $userid, "id" => $serviceids[$i]]);
            if(!$serviceid) {
                $apiresults = ["result" => "error", "message" => sprintf("Service ID %s not owned by Client ID provided", (int) $serviceids[$i])];
                return NULL;
            }
            $_SESSION["cart"]["addons"][] = ["id" => $addonid, "productid" => $serviceid, "qty" => $allowsQuantity === 2 && !empty($addonidsqty) ? $addonidsqty[$i] : 1, "allowsQuantity" => $allowsQuantity];
        }
    }
    $domainrenewals = $whmcs->get_req_var("domainrenewals");
    if($domainrenewals) {
        foreach ($domainrenewals as $domain => $regperiod) {
            $domain = mysql_real_escape_string($domain);
            $sql = "SELECT `id`\n                FROM `tbldomains`\n                WHERE userid=" . $userid . " AND domain='" . $domain . "' AND status IN ('Active', 'Expired', 'Grace', 'Redemption')";
            $domainResult = full_query($sql);
            $domainData = mysql_fetch_array($domainResult);
            if(isset($domainData["id"])) {
                $domainid = $domainData["id"];
            }
            if(!$domainid) {
                $sql = "SELECT `status`\n                    FROM `tbldomains`\n                    WHERE userid=" . $userid . " AND domain='" . $domain . "'";
                $domainResult = full_query($sql);
                $domainData = mysql_fetch_array($domainResult);
                $apiresults = ["result" => "error", "message" => ""];
                if(isset($domainData["status"])) {
                    $apiresults["message"] = "Domain status is set to '" . $domainData["status"] . "' and cannot be renewed";
                } else {
                    $apiresults["message"] = "Domain not owned by Client ID provided";
                }
                return NULL;
            }
            WHMCS\OrderForm::addDomainRenewalToCart($domainid, $regperiod);
        }
    }
    $processOnDemandRenewals = function ($renewalItems, string $renewalType) {
        if(!is_array($renewalItems)) {
            return NULL;
        }
        foreach ($renewalItems as $renewalId) {
            switch ($renewalType) {
                case "Addon":
                    $renewalModel = WHMCS\Service\ServiceAddonOnDemandRenewal::factoryByServiceId($renewalId);
                    $orderFormMethod = "addServiceAddonRenewalToCart";
                    break;
                case "Service":
                default:
                    $renewalModel = WHMCS\Service\ServiceOnDemandRenewal::factoryByServiceId($renewalId);
                    $orderFormMethod = "addServiceRenewalToCart";
                    if(is_null($renewalModel) || !$renewalModel->isRenewable()) {
                        throw new Exception("The system cannot renew " . $renewalType . " ID " . $renewalId . ".");
                    }
                    WHMCS\OrderForm::$orderFormMethod($renewalModel);
            }
        }
    };
    try {
        $processOnDemandRenewals($whmcs->getFromRequest("servicerenewals"), "Service");
        $processOnDemandRenewals($whmcs->getFromRequest("addonrenewals"), "Addon");
    } catch (Exception $e) {
        $apiresults = ["result" => "error", "message" => $e->getMessage()];
        return NULL;
    }
    $cartitems = (new WHMCS\OrderForm())->getNumItemsInCart($client);
    if(!$cartitems) {
        $apiresults = ["result" => "error", "message" => "No items added to cart so order cannot proceed"];
        return NULL;
    }
    $_SESSION["cart"]["ns1"] = $nameserver1 ?? NULL;
    $_SESSION["cart"]["ns2"] = $nameserver2 ?? NULL;
    $_SESSION["cart"]["ns3"] = $nameserver3 ?? NULL;
    $_SESSION["cart"]["ns4"] = $nameserver4 ?? NULL;
    $_SESSION["cart"]["paymentmethod"] = $paymentmethod;
    $_SESSION["cart"]["promo"] = $promocode ?? NULL;
    $_SESSION["cart"]["notes"] = $notes ?? NULL;
    $app = DI::make("app");
    if($app->isLocalApiRequest()) {
        $purchaseSource = WHMCS\Order\OrderPurchaseSource::LOCAL_API;
    } else {
        $purchaseSource = WHMCS\Order\OrderPurchaseSource::CLIENT_API;
    }
    (new WHMCS\OrderForm())->setCartDataByKey("orderPurchaseSource", $purchaseSource);
    if(isset($contactid) && $contactid) {
        $_SESSION["cart"]["contact"] = $contactid;
    }
    if(isset($noinvoice) && $noinvoice) {
        $_SESSION["cart"]["geninvoicedisabled"] = true;
    }
    if(isset($noinvoiceemail) && $noinvoiceemail) {
        $CONFIG["NoInvoiceEmailOnOrder"] = true;
    }
    if(isset($noemail) && $noemail) {
        $_SESSION["cart"]["orderconfdisabled"] = true;
    }
    $cartdata = calcCartTotals($client, true, false);
    if(isset($cartdata["result"]) && $cartdata["result"] == "error") {
        $apiresults = $cartdata;
        return NULL;
    }
    if($cartdata === false) {
        $apiresults = ["result" => "error", "message" => "No items remain in the cart. Order cannot proceed."];
        return NULL;
    }
    if(isset($affid) && $affid) {
        $verifyAffId = WHMCS\Database\Capsule::table("tblaffiliates")->where("id", $affid)->first();
    }
    if(isset($affid) && $affid && is_array($_SESSION["orderdetails"]["Products"]) && !empty($verifyAffId) && $_SESSION["uid"] != $verifyAffId->clientid) {
        foreach ($_SESSION["orderdetails"]["Products"] as $productid) {
            insert_query("tblaffiliatesaccounts", ["affiliateid" => $affid, "relid" => $productid]);
        }
    } else {
        unset($affid);
    }
    $productids = $addonids = $domainids = "";
    if(is_array($_SESSION["orderdetails"]["Products"])) {
        $productids = implode(",", $_SESSION["orderdetails"]["Products"]);
    }
    if(is_array($_SESSION["orderdetails"]["Addons"])) {
        $addonids = implode(",", $_SESSION["orderdetails"]["Addons"]);
    }
    if(is_array($_SESSION["orderdetails"]["Domains"])) {
        $domainids = implode(",", $_SESSION["orderdetails"]["Domains"]);
    }
    $apiresults = ["result" => "success", "orderid" => $_SESSION["orderdetails"]["OrderID"], "productids" => $productids, "serviceids" => $productids, "addonids" => $addonids, "domainids" => $domainids];
    if(empty($noinvoice)) {
        $apiresults["invoiceid"] = $_SESSION["orderdetails"]["InvoiceID"] ? $_SESSION["orderdetails"]["InvoiceID"] : get_query_val("tblorders", "invoiceid", ["id" => $_SESSION["orderdetails"]["OrderID"]]);
    }
}

?>