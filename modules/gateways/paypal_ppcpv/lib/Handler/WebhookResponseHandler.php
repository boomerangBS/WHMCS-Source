<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class WebhookResponseHandler extends AbstractHandler
{
    protected $handler;
    protected $acdcLog;
    public function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookEventRequest $request) : \WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookEventRequest
    {
        $responseHeader = self::eventReplyHeader(200, "OK");
        $initiatingModuleName = NULL;
        $webHookEvent = NULL;
        try {
            $webHookEvent = $this->eventApi()->receive($request);
            $initiatingModuleName = $webHookEvent->initiatingModule();
            $api = $this->selectApi($initiatingModuleName);
            if($this->moduleConfiguration->getSignatureVerificationSetting()) {
                $this->assertSignature($request, $api);
            }
            $outcomes = Event\AbstractWebhookHandler::newOutcomes();
            $responseMsg = $this->injectVaultTokenController($this->injectAPI($webHookEvent->getHandler(), $api), $this->selectModule($initiatingModuleName))->handle($webHookEvent, $outcomes);
            $this->log($initiatingModuleName)->gateway(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($webHookEvent->packEventRequest()), $responseMsg, ["history_id" => $outcomes->transactionHistoryId]);
        } catch (\WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookSignatureInvalid $e) {
        } catch (\WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseInvalid $e) {
        } catch (\WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseMalformed $e) {
            $responseHeader = self::eventReplyHeader(406, "Not Acceptable");
        } catch (\Exception $e) {
        } catch (\WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseNotFound $e) {
        } finally {
            if(isset($e)) {
                $debugData = !is_null($webHookEvent) ? $webHookEvent->packEventRequest() : $request->rawJson;
                $this->log($initiatingModuleName)->gateway(\WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($debugData), $e->getMessage());
                unset($debugData);
            }
            $this->log($initiatingModuleName)->module(!is_null($webHookEvent) ? (new \ReflectionClass($webHookEvent))->getShortName() : "Unknown Event", $request->rawJson, $responseHeader, "", []);
        }
    }
    public function assertSignature(\WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookEventRequest $request, $api) : \self
    {
        $verificationResponse = $this->verifyWebhookSignature($request, $this->moduleConfiguration->getWebhookIdentifier($this->env()), $api);
        if(!$verificationResponse->isValid()) {
            throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookSignatureInvalid("Webhook signature is invalid.");
        }
        return $this;
    }
    public static function eventReplyHeader($responseCode, string $responseMsg) : int
    {
        return $responseCode . " " . $responseMsg;
    }
    public function setACDCLog(\WHMCS\Module\Gateway\paypal_acdc\Logger $logger) : \self
    {
        $this->acdcLog = $logger;
        return $this;
    }
    public function eventApi() : \WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookController
    {
        return new \WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookController($this->module, $this->env(), $this->log);
    }
    public function injectVaultTokenController(Event\AbstractWebhookHandler $handler, $module) : Event\AbstractWebhookHandler
    {
        if(in_array("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\Event\\VaultTokenControllerRequired", class_uses($handler))) {
            $handler->setVaultTokenController(\WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController::factoryModule($module));
        }
        return $handler;
    }
    public function injectAPI(Event\AbstractWebhookHandler $handler, $api) : Event\AbstractWebhookHandler
    {
        if(in_array("WHMCS\\Module\\Gateway\\paypal_ppcpv\\Handler\\Event\\WebhookAPIControllerRequired", class_uses($handler))) {
            $handler->setAPI($api);
        }
        return $handler;
    }
    public function verifyWebhookSignature(\WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookEventRequest $webHookRequest, string $webhookIdentifier, $api) : \WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookSignatureVerifyResponse
    {
        try {
            $request = (new \WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookSignatureVerifyRequest($api))->setWebhookId($webhookIdentifier)->setAuthAlgo($webHookRequest->getHeaderFirstValue("PAYPAL-AUTH-ALGO"))->setCertUrl($webHookRequest->getHeaderFirstValue("PAYPAL-CERT-URL"))->setTransmission($webHookRequest->getHeaderFirstValue("PAYPAL-TRANSMISSION-ID"), $webHookRequest->getHeaderFirstValue("PAYPAL-TRANSMISSION-SIG"), $webHookRequest->getHeaderFirstValue("PAYPAL-TRANSMISSION-TIME"))->setEventBody($webHookRequest->rawJson);
        } catch (\InvalidArgumentException $e) {
            throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseMalformed(sprintf("Missing event verification metadata [%s]", $e->getMessage()));
        }
        $response = $api->send($request);
        if(!$response instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\WebhookSignatureVerifyResponse) {
            throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookSignatureInvalid(sprintf("Failed to verify webhook signature. [%s]", $response->__toString()));
        }
        return $response;
    }
    private function selectModule($initiatingModuleName) : \WHMCS\Module\Gateway
    {
        if($initiatingModuleName === \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME) {
            return \WHMCS\Module\Gateway\paypal_acdc\Core::loadModule();
        }
        return $this->module;
    }
    private function selectApi(string $initiatingModuleName)
    {
        if($initiatingModuleName === \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME) {
            return new \WHMCS\Module\Gateway\paypal_ppcpv\API\Controller($this->env(), $this->acdcLog);
        }
        return $this->api();
    }
    private function log(string $initiatingModuleName)
    {
        if($initiatingModuleName === \WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME) {
            return $this->acdcLog;
        }
        return $this->log;
    }
}

?>