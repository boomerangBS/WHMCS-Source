<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
class VisitTracking
{
    protected $entriesLimit;
    protected $namespace;
    protected $hits;
    const COOKIE_PREFIX = "Hits-";
    public function __construct($namespace, $entriesLimit)
    {
        $this->namespace = $namespace;
        $this->entriesLimit = $entriesLimit;
        $this->readCookie();
    }
    protected function getNamespace()
    {
        return $this->namespace;
    }
    protected function getCookieName()
    {
        return self::COOKIE_PREFIX . $this->getNamespace();
    }
    protected function readCookie()
    {
        $this->hits = collect(Cookie::get($this->getCookieName(), true));
    }
    protected function setCookie()
    {
        $data = Input\Sanitize::decode($this->hits->toArray());
        return Cookie::set($this->getCookieName(), $data);
    }
    public function log($pageTitle, $requestUri = NULL)
    {
        if(is_null($requestUri)) {
            $requestUri = $this->getRequestUri();
        }
        foreach ($this->hits->where("href", $requestUri)->keys() as $key) {
            unset($this->hits[$key]);
        }
        if($this->entriesLimit <= $this->hits->count()) {
            $this->hits->shift();
        }
        $this->hits->push(["href" => $requestUri, "title" => $pageTitle]);
        $this->setCookie();
    }
    public function get()
    {
        return $this->hits;
    }
    protected function getRequestUri()
    {
        $requestUri = $_SERVER["REQUEST_URI"];
        $queryString = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "";
        if($queryString) {
            $requestUri = str_replace("?" . $queryString, "", $requestUri);
        }
        if(isset($_REQUEST["rp"])) {
            $requestUri .= "?rp=" . $_REQUEST["rp"];
        }
        return $requestUri;
    }
}

?>