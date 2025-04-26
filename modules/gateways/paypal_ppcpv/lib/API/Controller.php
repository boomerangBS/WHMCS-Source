<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Module\Gateway\paypal_ppcpv\API;

// Decoded file for php version 72.
class Controller
{
    protected $environment;
    protected $log;
    public function __construct(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $e, \WHMCS\Module\Gateway\paypal_ppcpv\Logger $log)
    {
        $this->withEnv($e);
        $this->log = $log;
    }
    public static function generateTraceIdentifier()
    {
        $rng = function () {
            return mt_rand(0, PHP_INT_MAX);
        };
        $trace = "";
        while (strlen($trace) < 32) {
            $trace .= dechex($rng());
        }
        return substr($trace, 0, 19);
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
    public function send(AbstractRequest $request) : AbstractResponse
    {
        if(!$request->sendReady()) {
            throw new \Exception(sprintf("%s: %s incomplete, unable to send", \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::MODULE_NAME, get_class($request)));
        }
        $injectResponse = $this->injectAccessToken($request);
        if(!is_null($injectResponse)) {
            $this->logRequestAttempt($request, NULL, sprintf("Failed to acquire access token\n%s", $injectResponse->__toString()));
            return $injectResponse;
        }
        unset($injectResponse);
        $wireOut = NULL;
        $httpResponse = $request->observeWireOut(function ($wireRequest) {
            static $wireOut = NULL;
            $wireOut = $wireRequest;
        })->send();
        try {
            $error = $this->detectError($httpResponse);
            if(!is_null($error)) {
                $response = $error;
            } else {
                $response = $request->responseType();
                $response->respond($httpResponse);
            }
        } catch (\Throwable $t) {
            $response = new GenericErrorResponse();
            $response->error = "Unrecognized Error";
            $response->error_description = $t->getMessage();
        } finally {
            $this->logRequestResponse($request, $wireOut, $response, $httpResponse);
        }
    }
    protected function detectError(HttpResponse $response) : AbstractErrorResponse
    {
        if($response->isSuccess()) {
            return NULL;
        }
        $error = RESTErrorResponse::factory($response->body);
        if(is_null($error)) {
            $error = GenericErrorResponse::factory($response->body);
            if(is_null($error)) {
                $error = new InternalErrorResponse($response);
            }
        }
        return $error;
    }
    public function injectAccessToken(AbstractRequest $request) : AbstractErrorResponse
    {
        if(!in_array("WHMCS\\Module\\Gateway\\paypal_ppcpv\\API\\RequestAccessTokenAuthenticatedTrait", class_uses($request))) {
            return NULL;
        }
        $tokenResponse = $this->getAccessToken();
        if(!$tokenResponse instanceof AccessTokenResponse) {
            return $tokenResponse;
        }
        $request->accessToken($tokenResponse->token());
    }
    public function getAccessToken() : AbstractResponse
    {
        $tokenResponse = $this->loadAccessToken();
        if(is_null($tokenResponse) || $tokenResponse->isStale()) {
            $tokenResponse = $this->refreshAccessToken();
            if(!$tokenResponse instanceof AccessTokenResponse) {
                return $tokenResponse;
            }
        }
        return $tokenResponse;
    }
    public function refreshAccessToken() : AbstractResponse
    {
        $response = $this->send(new AccessTokenRequest($this));
        if($response instanceof AccessTokenResponse) {
            $this->persistAccessToken($response);
        }
        return $response;
    }
    protected function loadAccessToken() : AccessTokenResponse
    {
        $existingToken = \WHMCS\TransientData::getInstance()->retrieve($this->cacheKeyAccessToken()) ?? "";
        if($existingToken == "") {
            return NULL;
        }
        $existingToken = decrypt($existingToken);
        if($existingToken == "") {
            return NULL;
        }
        return (new AccessTokenResponse())->unpack($existingToken);
    }
    protected function persistAccessToken(AccessTokenResponse $token) : void
    {
        \WHMCS\TransientData::getInstance()->store($this->cacheKeyAccessToken(), encrypt($token->pack()), $token->expiresInSeconds(\DateTimeImmutable::createFromMutable($token->nowUTC())));
    }
    protected function cacheKeyAccessToken(\WHMCS\Module\Gateway\paypal_ppcpv\Environment $env) : \WHMCS\Module\Gateway\paypal_ppcpv\Environment
    {
        if(is_null($env)) {
            $env = $this->env();
        }
        return sprintf("%s-access-token-%s", \WHMCS\Module\Gateway\paypal_ppcpv\PayPalCommerce::transientDataPrefix(), $env->label);
    }
    public function purgeCache()
    {
        \WHMCS\TransientData::getInstance()->delete($this->cacheKeyAccessToken());
    }
    protected function logRequestResponse(AbstractRequest $request, $wireOut, $response, $httpResponse) : void
    {
        $this->log->module((new \ReflectionClass($request))->getShortName(), !is_null($wireOut) ? $wireOut->__toString() : "", (new \ReflectionClass($response))->getShortName(), $httpResponse->__toString(), []);
    }
    protected function logRequestAttempt(AbstractRequest $request, $wireOut, string $attempted) : void
    {
        $requestClass = (new \ReflectionClass($request))->getShortName();
        $this->log->module($requestClass, !is_null($wireOut) ? $wireOut->__toString() : "", "", $attempted, []);
    }
}

?>