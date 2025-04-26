<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
abstract class AbstractWebhookEvent
{
    protected $expectedPayloadProperties = [];
    protected $request;
    public $links = [];
    public function setRequest(WebhookEventRequest $request) : \self
    {
        $this->request = $request;
        return $this;
    }
    public function getRequest() : WebhookEventRequest
    {
        return $this->request;
    }
    public function withJSON($json) : \self
    {
        $decoded = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($json);
        if($decoded === false) {
            throw new \Exception("Malformed JSON");
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\Util::overlayMapOnObject($decoded->resource, $this);
    }
    public function getWebhookId()
    {
        if(!is_null($this->request)) {
            return $this->request->id;
        }
        return "";
    }
    public function getSummary()
    {
        if(!is_null($this->request)) {
            return $this->request->summary;
        }
        return "";
    }
    public function getEventType()
    {
        if(!is_null($this->request)) {
            return $this->request->event_type;
        }
        return "";
    }
    public function assertValidPayload() : \self
    {
        if(strlen($this->getWebhookId()) == 0 || strlen($this->getEventType()) == 0 || is_null($this->request) || !$this->assertExpectedPayload()) {
            throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseInvalid("Webhook payload is not valid");
        }
        return $this;
    }
    public abstract function getHandler() : \WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event\AbstractWebhookHandler;
    public abstract function initiatingModule();
    public function getLinkByRelation($relation)
    {
        if(!is_null($this->links)) {
            foreach ($this->links as $link) {
                if($link->rel === $relation) {
                    return $link->href;
                }
            }
        }
        return NULL;
    }
    protected function isExpectedPayload($firstMissing)
    {
        $expected = $this->expectedPayloadProperties;
        foreach ($expected as $keyString) {
            $sourcePointer =& $this;
            foreach (explode("->", $keyString) as $key) {
                if(isset($sourcePointer->{$key})) {
                    if(is_object($sourcePointer->{$key})) {
                        $sourcePointer =& $sourcePointer->{$key};
                    }
                } else {
                    $firstMissing = $key;
                    return false;
                }
            }
        }
        return true;
    }
    protected function responseUnexpected($memo)
    {
        if(!empty($memo)) {
            $memo = " (" . $memo . ")";
        }
        return "Unexpected Payload Structure" . $memo;
    }
    protected function assertExpectedPayload()
    {
        $missing = "";
        if(!$this->isExpectedPayload($missing)) {
            throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseMalformed($this->responseUnexpected("missing '" . $missing . "'"));
        }
        unset($missing);
        return true;
    }
    public function packEventRequest()
    {
        return $this->getRequest()->rawJson;
    }
}

?>