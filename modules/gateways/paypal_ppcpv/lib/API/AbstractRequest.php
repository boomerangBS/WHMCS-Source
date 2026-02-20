<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\API;
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F6D6F64756C65732F67617465776179732F70617970616C5F70706370762F6C69622F4150492F4162737472616374526571756573742E7068703078376664353934323461393835_
{
    public $url;
    public $curlOptions;
    public $payload;
    public $trace;
    public function __toString()
    {
        unset($this->curlOptions["CURLOPT_HEADER"]);
        $method = "GET";
        if(isset($this->curlOptions["CURLOPT_CUSTOMREQUEST"])) {
            $method = $this->curlOptions["CURLOPT_CUSTOMREQUEST"];
            unset($this->curlOptions["CURLOPT_CUSTOMREQUEST"]);
        } elseif(isset($this->curlOptions["CURLOPT_POST"])) {
            $method = "POST";
            unset($this->curlOptions["CURLOPT_POST"]);
        }
        $headers = "";
        foreach ($this->curlOptions["CURLOPT_HTTPHEADER"] ?? [] as $header) {
            $headers .= $header . "\n";
        }
        unset($this->curlOptions["CURLOPT_HTTPHEADER"]);
        $curlOptions = NULL;
        if(!empty($this->curlOptions)) {
            $curlOptions = var_export($this->curlOptions, true);
        }
        return sprintf("%s %s\n%s\n%s\n%s", $method, $this->url, $this->payload, $headers, $curlOptions);
    }
}
abstract class AbstractRequest
{
    protected $controller;
    protected $requestPreProcessors = [];
    protected $wireObservers;
    protected $traceIdentifier = "";
    public function __construct(Controller $c)
    {
        $this->controller = $c;
        $this->wireObservers = new \SplObjectStorage();
        $this->responseHeaders();
    }
    public function env() : \WHMCS\Module\Gateway\paypal_ppcpv\Environment
    {
        return $this->controller->env();
    }
    public abstract function send() : HttpResponse;
    public abstract function sendReady();
    protected abstract function payload();
    public abstract function responseType() : AbstractResponse;
    public function isResponseType(AbstractResponse $response) : AbstractResponse
    {
        $type = $this->responseType();
        return $response instanceof $type;
    }
    protected function partnerAttribution() : \self
    {
        return $this->header("PayPal-Partner-Attribution-Id", $this->env()->attributionId);
    }
    protected function bearerAuthorization($token) : \self
    {
        return $this->header("Authorization", "Bearer " . $token);
    }
    protected function basicAuthentication($username, string $password) : \self
    {
        $this->requestPreProcessors[] = function (&$payload, &$curlOptions) use($username, $password) {
            $curlOptions["CURLOPT_USERPWD"] = sprintf("%s:%s", $username, $password);
        };
        return $this;
    }
    protected function preferRepresentationResponse() : \self
    {
        $this->header("Prefer", "'return=representation'");
        return $this;
    }
    protected function traceIdentifier($trace) : \self
    {
        $this->header("PayPal-Request-Id", $trace);
        $this->traceIdentifier = $trace;
        return $this;
    }
    protected function injectTrace()
    {
        $trace = Controller::generateTraceIdentifier();
        $this->traceIdentifier($trace);
        return $trace;
    }
    protected function contentJSON() : \self
    {
        return $this->content("application/json");
    }
    protected function contentURLEncoded() : \self
    {
        return $this->content("application/x-www-form-urlencoded");
    }
    protected function contentPlain() : \self
    {
        return $this->content("text/plain");
    }
    protected function content($mime) : \self
    {
        return $this->header("Content-Type", $mime);
    }
    protected function accept($mime) : \self
    {
        return $this->header("Accept", $mime);
    }
    protected function acceptJSON() : \self
    {
        return $this->accept("application/json");
    }
    protected function responseHeaders() : \self
    {
        $this->requestPreProcessors[] = function (&$payload, &$curlOptions) {
            $curlOptions["CURLOPT_HEADER"] = 1;
        };
        return $this;
    }
    protected function header($header, string $value) : \self
    {
        $this->requestPreProcessors[] = function (&$payload, &$curlOptions) use($header, $value) {
            $curlOptions["CURLOPT_HTTPHEADER"][] = sprintf("%s: %s", $header, $value);
        };
        return $this;
    }
    protected function post($endpoint, string $payload) : HttpResponse
    {
        $this->requestPreProcessors[] = function (&$payload, &$curlOptions) {
            $curlOptions["CURLOPT_POST"] = true;
        };
        return $this->wire($endpoint, $payload);
    }
    protected function get($endpoint = "", string $query) : HttpResponse
    {
        if($query != "") {
            $endpoint .= $query;
        }
        return $this->wire($endpoint, NULL);
    }
    protected function delete($endpoint = "", string $query) : HttpResponse
    {
        if($query != "") {
            $endpoint .= $query;
        }
        $this->requestPreProcessors[] = function (&$payload, &$curlOptions) {
            $curlOptions["CURLOPT_CUSTOMREQUEST"] = "DELETE";
        };
        return $this->wire($endpoint, NULL);
    }
    protected function patch($endpoint, string $payload) : HttpResponse
    {
        $this->requestPreProcessors[] = function (&$payload, &$curlOptions) {
            $curlOptions["CURLOPT_CUSTOMREQUEST"] = "PATCH";
        };
        return $this->wire($endpoint, $payload);
    }
    private function wire($endpoint, string $payload) : HttpResponse
    {
        $this->injectTrace();
        $curlOptions = [];
        foreach ($this->requestPreProcessors as $processor) {
            $processor($payload, $curlOptions);
        }
        $fqUrl = $this->env()->apiURL . $endpoint;
        foreach ($this->wireObservers as $observer) {
            $wire = $this->wireRequestType();
            $wire->url = $fqUrl;
            $wire->curlOptions = $curlOptions;
            $wire->payload = $payload;
            $wire->trace = $this->traceIdentifier;
            $observer($wire);
        }
        unset($observer);
        unset($wire);
        $ch = curlCall($fqUrl, $payload, $curlOptions, true);
        $rawResponse = curl_exec($ch);
        $headersLength = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $rawHeaders = substr($rawResponse, 0, $headersLength);
        $rawBody = substr($rawResponse, $headersLength);
        return (new HttpResponse())->withBody($rawBody)->withStatusCode((int) curl_getinfo($ch, CURLINFO_HTTP_CODE))->withHeaderString($rawHeaders);
    }
    public function observeWireOut($f) : \self
    {
        $this->wireObservers->attach($f);
        return $this;
    }
    public function wireRequestType()
    {
        return new func_num_args();
    }
}

?>