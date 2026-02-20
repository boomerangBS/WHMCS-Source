<?php

if(!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}
$GATEWAYMODULE["bluepayname"] = "bluepay";
$GATEWAYMODULE["bluepayvisiblename"] = "BluePay";
$GATEWAYMODULE["bluepaytype"] = "CC";
if(!class_exists("BluePayment")) {
    class BluePayment
    {
        public $accountId;
        public $userId;
        public $tps;
        public $transType;
        public $payType;
        public $mode;
        public $masterId;
        public $secretKey;
        public $account;
        public $cvv2;
        public $expire;
        public $ssn;
        public $birthdate;
        public $custId;
        public $custIdState;
        public $amount;
        public $name1;
        public $name2;
        public $addr1;
        public $addr2;
        public $city;
        public $state;
        public $zip;
        public $country;
        public $phone;
        public $email;
        public $memo;
        public $orderId;
        public $invoiceId;
        public $tip;
        public $tax;
        public $doRebill;
        public $rebDate;
        public $rebExpr;
        public $rebCycles;
        public $rebAmount;
        public $doAutocap;
        public $avsAllowed;
        public $cvv2Allowed;
        public $response;
        public $transId;
        public $status;
        public $avsResp;
        public $cvv2Resp;
        public $authCode;
        public $message;
        public $rebid;
        public function __construct($account = ACCOUNT_ID, $key = SECRET_KEY, $mode = MODE)
        {
            $this->accountId = $account;
            $this->secretKey = $key;
            $this->mode = $mode;
            $this->payType = "CREDIT";
        }
        public function sale($amount)
        {
            $this->transType = "SALE";
            $this->amount = BluePayment::formatAmount($amount);
        }
        public function rebSale($transId, $amount = NULL)
        {
            $this->masterId = $transId;
            $this->sale($amount);
        }
        public function auth($amount)
        {
            $this->transType = "AUTH";
            $this->amount = BluePayment::formatAmount($amount);
        }
        public function autocapAuth($amount, $avsAllow = NULL, $cvv2Allow = NULL)
        {
            $this->auth($amount);
            $this->setAutocap();
            $this->addAvsProofing($avsAllow);
            $this->addCvv2Proofing($avsAllow);
        }
        public function addLevel2Qual($orderId = NULL, $invoiceId = NULL, $tip = NULL, $tax = NULL)
        {
            $this->orderId = $orderId;
            $this->invoiceId = $invoiceId;
            $this->tip = $tip;
            $this->tax = $tax;
        }
        public function refund($transId, $amount = NULL)
        {
            $this->transType = "REFUND";
            $this->masterId = $transId;
            if(isset($amount)) {
                $this->amount = self::formatAmount($amount);
            }
        }
        public function capture($transId)
        {
            $this->transType = "CAPTURE";
            $this->masterId = $transId;
        }
        public function rebCancel($transId)
        {
            $this->transType = "REBCANCEL";
            $this->masterId = $transId;
        }
        public function rebAdd($amount, $date, $expr, $cycles)
        {
            $this->doRebill = "1";
            $this->rebAmount = BluePayment::formatAmount($amount);
            $this->rebDate = $date;
            $this->rebExpr = $expr;
            $this->rebCycles = $cycles;
        }
        public function addAvsProofing($allow)
        {
            $this->avsAllowed = $allow;
        }
        public function addCvv2Proofing($allow)
        {
            $this->cvv2Allowed = $allow;
        }
        public function setAutocap()
        {
            $this->doAutocap = "1";
        }
        public function setCustInfo($account, $cvv2, $expire, $name1, $name2, $addr1, $city, $state, $zip, $country, $phone, $email, $addr2 = NULL, $memo = NULL)
        {
            $this->account = $account;
            $this->cvv2 = $cvv2;
            $this->expire = $expire;
            $this->name1 = $name1;
            $this->name2 = $name2;
            $this->addr1 = $addr1;
            $this->addr2 = $addr2;
            $this->city = $city;
            $this->state = $state;
            $this->zip = $zip;
            $this->country = $country;
            $this->phone = $phone;
            $this->email = $email;
            $this->memo = $memo;
        }
        public function formatAmount($amount)
        {
            return sprintf("%01.2F", (double) $amount);
        }
        public function setOrderId($orderId)
        {
            $this->orderId = $orderId;
        }
        public function calcTPS()
        {
            $hashstr = $this->secretKey . $this->accountId . $this->transType . $this->amount . $this->masterId . $this->name1 . $this->account;
            return md5($hashstr);
        }
        public function process()
        {
            $tps = $this->calcTPS();
            $fields = ["ACCOUNT_ID" => $this->accountId, "USER_ID" => $this->userId, "TAMPER_PROOF_SEAL" => $tps, "TRANS_TYPE" => $this->transType, "PAYMENT_TYPE" => $this->payType, "MODE" => $this->mode, "MASTER_ID" => $this->masterId, "PAYMENT_ACCOUNT" => $this->account, "CARD_CVV2" => $this->cvv2, "CARD_EXPIRE" => $this->expire, "SSN" => $this->ssn, "BIRTHDATE" => $this->birthdate, "CUST_ID" => $this->custId, "CUST_ID_STATE" => $this->custIdState, "AMOUNT" => $this->amount, "NAME1" => $this->name1, "NAME2" => $this->name2, "ADDR1" => $this->addr1, "ADDR2" => $this->addr2, "CITY" => $this->city, "STATE" => $this->state, "ZIP" => $this->zip, "COUNTRY" => $this->country, "PHONE" => $this->phone, "EMAIL" => $this->email, "MEMO" => $this->memo, "ORDER_ID" => $this->orderId, "INVOICE_ID" => $this->invoiceId, "AMOUNT_TIP" => $this->tip, "AMOUNT_TAX" => $this->tax, "DO_REBILL" => $this->doRebill, "REB_FIRST_DATE" => $this->rebDate, "REB_EXPR" => $this->rebExpr, "REB_CYCLES" => $this->rebCycles, "REB_AMOUNT" => $this->rebAmount, "DO_AUTOCAP" => $this->doAutocap, "AVS_ALLOWED" => $this->avsAllowed, "CVV2_ALLOWED" => $this->cvv2Allowed];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, POST_URL);
            curl_setopt($ch, CURLOPT_USERAGENT, "BluepayPHP SDK/2.0");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
            $this->response = curl_exec($ch);
            curl_close($ch);
            $this->parseResponse();
        }
        public function parseResponse()
        {
            parse_str($this->response, $array);
            $this->transId = $array["TRANS_ID"];
            $this->status = $array["STATUS"];
            $this->avsResp = $array["AVS"];
            $this->cvv2Resp = $array["CVV2"];
            $this->authCode = $array["AUTH_CODE"];
            $this->message = $array["MESSAGE"];
            $this->rebid = $array["REBID"];
        }
        public function getResponse()
        {
            return $this->response;
        }
        public function getTransId()
        {
            return $this->transId;
        }
        public function getStatus()
        {
            return $this->status;
        }
        public function getAvsResp()
        {
            return $this->avsResp;
        }
        public function getCvv2Resp()
        {
            return $this->cvv2Resp;
        }
        public function getAuthCode()
        {
            return $this->authCode;
        }
        public function getMessage()
        {
            return $this->message;
        }
        public function getRebid()
        {
            return $this->rebid;
        }
    }
}
if(!function_exists("toString")) {
    function toString($string)
    {
        if(preg_match("/ /", $string)) {
            $elements = explode(" ", $string);
            $string = "";
            $f = true;
            foreach ($elements as $elem) {
                if($f) {
                    $string .= $elem;
                    $f = false;
                } else {
                    $string .= "+" . $elem;
                }
            }
        }
        return $string;
    }
}
if(!function_exists("http_build_query")) {
    function http_build_query(&$data)
    {
        $keys = array_keys($data);
        $string = "";
        $f = true;
        foreach ($keys as $key) {
            if($f) {
                $string .= $key . "=" . toString($data[$key]);
                $f = false;
            } else {
                $string .= "&" . $key . "=" . toString($data[$key]);
            }
        }
        return $string;
    }
}
function bluepay_activate()
{
    defineGatewayField("bluepay", "text", "accountid", "", "Account ID", "20", "");
    defineGatewayField("bluepay", "text", "secretkey", "", "Secret Key", "40", "");
    defineGatewayField("bluepay", "yesno", "testmode", "", "Demo Mode", "", "");
}
function bluepay_capture($params)
{
    if($params["testmode"] == "on") {
        $gateway_testmode = "TEST";
    } else {
        $gateway_testmode = "LIVE";
    }
    define("MODE", $gateway_testmode);
    define("POST_URL", "https://secure.bluepay.com/interfaces/bp20post");
    define("ACCOUNT_ID", $params["accountid"]);
    define("SECRET_KEY", $params["secretkey"]);
    define("STATUS_DECLINE", "0");
    define("STATUS_APPROVED", "1");
    define("STATUS_ERROR", "2");
    $bp = new BluePayment();
    $bp->sale($params["amount"]);
    $bp->setCustInfo($params["cardnum"], $params["cccvv"], $params["cardexp"], $params["clientdetails"]["firstname"], $params["clientdetails"]["lastname"], $params["clientdetails"]["address1"], $params["clientdetails"]["city"], $params["clientdetails"]["state"], $params["clientdetails"]["postcode"], $params["clientdetails"]["country"], $params["clientdetails"]["phonenumber"], $params["clientdetails"]["email"]);
    $bp->invoiceId = $params["invoiceid"];
    $bp->process();
    $desc = "Action => Capture\nClient => " . $params["clientdetails"]["firstname"] . " " . $params["clientdetails"]["lastname"] . "\n";
    $desc .= "TransId => " . $bp->getTransId() . "\n" . "Status => " . $bp->getStatus() . "\n" . "AVS Resp => " . $bp->getAvsResp() . "\n" . "CVV2 Resp => " . $bp->getCvv2Resp() . "\n" . "Auth Code => " . $bp->getAuthCode() . "\n" . "Message => " . $bp->getMessage() . "\n";
    $bp->getStatus();
    switch ($bp->getStatus()) {
        case "1":
            return ["status" => "success", "transid" => $bp->getTransId(), "rawdata" => $desc];
            break;
        case "0":
            return ["status" => "declined", "rawdata" => $desc];
            break;
        default:
            return ["status" => "error", "rawdata" => $desc];
    }
}
function bluepay_refund($params)
{
    if($params["testmode"] == "on") {
        $gateway_testmode = "TEST";
    } else {
        $gateway_testmode = "LIVE";
    }
    define("MODE", $gateway_testmode);
    define("POST_URL", "https://secure.bluepay.com/interfaces/bp20post");
    define("ACCOUNT_ID", $params["accountid"]);
    define("SECRET_KEY", $params["secretkey"]);
    define("STATUS_DECLINE", "0");
    define("STATUS_APPROVED", "1");
    define("STATUS_ERROR", "2");
    $bp = new BluePayment();
    $bp->refund($params["transid"], $params["amount"]);
    $bp->setCustInfo($params["cardnum"], "", $params["cardexp"], $params["clientdetails"]["firstname"], $params["clientdetails"]["lastname"], $params["clientdetails"]["address1"], $params["clientdetails"]["city"], $params["clientdetails"]["state"], $params["clientdetails"]["postcode"], $params["clientdetails"]["country"], $params["clientdetails"]["phonenumber"], $params["clientdetails"]["email"]);
    $bp->process();
    $desc = "Action => Refund\nClient => " . $params["clientdetails"]["firstname"] . " " . $params["clientdetails"]["lastname"] . "\n";
    $desc .= "TransId => " . $bp->getTransId() . "\n" . "Status => " . $bp->getStatus() . "\n" . "AVS Resp => " . $bp->getAvsResp() . "\n" . "CVV2 Resp => " . $bp->getCvv2Resp() . "\n" . "Auth Code => " . $bp->getAuthCode() . "\n" . "Message => " . $bp->getMessage() . "\n";
    $bp->getStatus();
    switch ($bp->getStatus()) {
        case "1":
            return ["status" => "success", "transid" => $bp->getTransId(), "rawdata" => $desc];
            break;
        default:
            return ["status" => "declined", "rawdata" => $desc];
    }
}

?>