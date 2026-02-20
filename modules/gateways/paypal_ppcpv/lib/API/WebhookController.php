<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class WebhookController
{
    protected $mapWebhookEvents = ["PAYMENT.CAPTURE.COMPLETED" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentCaptureCompletedEvent", "PAYMENT.CAPTURE.DENIED" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentCaptureDeniedEvent", "PAYMENT.CAPTURE.DECLINED" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentCaptureDeclinedEvent", "PAYMENT.CAPTURE.REFUNDED" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentCaptureRefundedEvent", "PAYMENT.CAPTURE.REVERSED" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentCaptureReversedEvent", "PAYMENT.CAPTURE.PENDING" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\PaymentCapturePendingEvent", "VAULT.PAYMENT-TOKEN.CREATED" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\VaultPaymentTokenCreatedEvent", "VAULT.PAYMENT-TOKEN.DELETED" => "WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\VaultPaymentTokenDeletedEvent"];
    protected $environment;
    protected $module;
    protected $log;
    public function __construct(\WHMCS\Module\Gateway $module, \WHMCS\Module\Gateway\paypal_ppcpv\Environment $e, \WHMCS\Module\Gateway\paypal_ppcpv\Logger $log)
    {
        $this->module = $module;
        $this->withEnv($e);
        $this->log = $log;
    }
    public function env() : \WHMCS\Module\Gateway\paypal_ppcpv\Environment
    {
        return $this->environment;
    }
    public function withEnv(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $e) : \self
    {
        $this->environment = $e;
        return $this;
    }
    public function receive(WebhookEventRequest $event) : AbstractWebhookEvent
    {
        try {
            $error = $this->detectError($event);
            if(!is_null($error)) {
                $webHookEvent = $error;
            } else {
                $webHookEvent = $event->castAs($this->getWebhookEventResponse($event->event_type))->setRequest($event)->assertValidPayload();
            }
        } catch (\WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseNotFound $e) {
            throw $e;
        }
        return $webHookEvent;
    }
    protected function detectError(WebhookEventRequest $request) : AbstractErrorResponse
    {
        if(!empty($request->id) && !empty($request->resource)) {
            return NULL;
        }
        return RESTErrorResponse::factory($request->rawJson);
    }
    private function getWebhookEventResponse($webhookEvent) : AbstractWebhookEvent
    {
        if(isset($this->mapWebhookEvents[$webhookEvent])) {
            return new $this->mapWebhookEvents[$webhookEvent]();
        }
        throw new \WHMCS\Module\Gateway\paypal_ppcpv\Exception\WebhookResponseNotFound("No webhook event response found: " . $webhookEvent);
    }
}

?>