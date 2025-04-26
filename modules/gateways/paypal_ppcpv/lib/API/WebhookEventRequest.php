<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class WebhookEventRequest
{
    public $id = "";
    public $event_type = "";
    public $create_time;
    public $summary = "";
    public $resource_type = "";
    public $resource = "";
    public $headers = [];
    public $rawJson = "";
    public static function factory($headers, string $body) : \self
    {
        return (new self())->withHeaderArray($headers)->withJSON($body);
    }
    public function withHeaderArray($headers) : \self
    {
        $this->headers = $headers;
        return $this;
    }
    public function withJSON(string $json)
    {
        $this->rawJson = $json;
        $decoded = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($json);
        if($decoded === false) {
            throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseMalformed("Malformed JSON");
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($decoded, $this);
    }
    public function getHeader($header) : array
    {
        $header = strtolower($header);
        if(!isset($this->headers[$header])) {
            return [];
        }
        return $this->headers[$header];
    }
    public function getHeaderFirstValue($header)
    {
        return $this->getHeader($header)[0] ?? "";
    }
    public function castAs(AbstractWebhookEvent $newClass)
    {
        return (new $newClass())->withJSON($this->rawJson);
    }
}

?>