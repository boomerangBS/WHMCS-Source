<?php

namespace WHMCS\Module\Registrar\CentralNic;

class RRPProxyController extends AbstractController
{
    protected $api;
    protected $sandboxUrl = "https://api-ote.rrpproxy.net/api/call.cgi";
    protected $liveUrl = "https://api.rrpproxy.net/api/call.cgi";
    protected $customerHeader = "CNR/WHMCS/";
    public function __construct(array $params)
    {
        $url = $this->liveUrl;
        $password = $params["Password"] ?? "";
        if($params["TestMode"] == "on") {
            $url = $this->sandboxUrl;
        }
        $api = (new Api\RRPProxyApi($url, $params["Username"] ?? "", $password, new Api\StringParser(), new CurlCall()))->setCustomHeader($this->customerHeader . \App::getVersion()->getRelease(2));
        if(!empty($params["ProxyServer"])) {
            $api->setProxy($params["ProxyServer"]);
        }
        $this->api = $api;
        parent::__construct($params);
    }
}

?>