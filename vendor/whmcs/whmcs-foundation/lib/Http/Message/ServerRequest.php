<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Http\Message;

class ServerRequest extends \Laminas\Diactoros\ServerRequest
{
    protected $queryBag;
    protected $requestBag;
    protected $attributesBag;
    protected $jsonAttributes;
    public static function fromGlobals(array $server = NULL, array $query = NULL, array $body = NULL, array $cookies = NULL, array $files = NULL)
    {
        $stdRequest = \Laminas\Diactoros\ServerRequestFactory::fromGlobals($server, $query, $body, $cookies, $files);
        $ourRequest = new self($stdRequest->getServerParams(), $stdRequest->getUploadedFiles(), $stdRequest->getUri(), $stdRequest->getMethod(), $stdRequest->getBody(), $stdRequest->getHeaders(), $stdRequest->getCookieParams(), $stdRequest->getQueryParams(), $stdRequest->getParsedBody(), $stdRequest->getProtocolVersion());
        return $ourRequest;
    }
    public function withQueryParams($query) : \Laminas\Diactoros\ServerRequest
    {
        $this->queryBag = NULL;
        return parent::withQueryParams($query);
    }
    public function query()
    {
        if(!$this->queryBag) {
            $this->queryBag = new \Symfony\Component\HttpFoundation\ParameterBag((array) $this->getQueryParams());
        }
        return $this->queryBag;
    }
    public function withParsedBody($data) : \Laminas\Diactoros\ServerRequest
    {
        $this->requestBag = NULL;
        return parent::withParsedBody($data);
    }
    public function request()
    {
        if(!$this->requestBag) {
            $this->requestBag = new \Symfony\Component\HttpFoundation\ParameterBag((array) $this->getParsedBody());
        }
        return $this->requestBag;
    }
    public function getResponseType()
    {
        $responseFactory = \DI::make("Route\\ResponseType");
        return $responseFactory->getMappedRoute($this->getAttribute("matchedRouteHandle"));
    }
    public function expectsJsonResponse()
    {
        if($this->getResponseType() == ResponseFactory::RESPONSE_TYPE_JSON) {
            return true;
        }
        if($this->isXHR()) {
            return true;
        }
        return false;
    }
    public function isAdminRequest()
    {
        return (bool) $this->getAttribute(\WHMCS\Route\Middleware\RoutableAdminRequestUri::ATTRIBUTE_ADMIN_REQUEST);
    }
    public function isApiV1Request()
    {
        return (bool) $this->getAttribute(\WHMCS\Route\Middleware\RoutableApiRequestUri::ATTRIBUTE_API_V1_REQUEST);
    }
    public function getApiVersion()
    {
        if($this->isApiV1Request()) {
            return "v1";
        }
        return $this->getAttribute(\WHMCS\Route\Middleware\RoutableApiRequestUri::ATTRIBUTE_API_NG_VERSION);
    }
    public function isApiNGRequest()
    {
        return (bool) $this->getAttribute(\WHMCS\Route\Middleware\RoutableApiRequestUri::ATTRIBUTE_API_NG_VERSION);
    }
    public function isXHR()
    {
        return (bool) (strtolower($this->getHeaderLine("X-Requested-With")) === "xmlhttprequest");
    }
    public function withAttribute($attribute, $value) : \Laminas\Diactoros\ServerRequest
    {
        $this->attributesBag = NULL;
        return parent::withAttribute($attribute, $value);
    }
    public function withoutAttribute($attribute) : \Laminas\Diactoros\ServerRequest
    {
        $this->attributesBag = NULL;
        return parent::withoutAttribute($attribute);
    }
    public function attributes()
    {
        if(!$this->attributesBag) {
            $this->attributesBag = new \Symfony\Component\HttpFoundation\ParameterBag((array) $this->getAttributes());
        }
        return $this->attributesBag;
    }
    public function has($key)
    {
        if($this->query()->has($key) || $this->attributes()->has($key) || $this->request()->has($key)) {
            return true;
        }
        return false;
    }
    public function get($key, $default = NULL)
    {
        if($this !== ($result = $this->query()->get($key, $this))) {
            return $result;
        }
        if($this !== ($result = $this->attributes()->get($key, $this))) {
            return $result;
        }
        if($this !== ($result = $this->request()->get($key, $this))) {
            return $result;
        }
        return $default;
    }
    public function getState() : \WHMCS\Api\NG\Versions\V2\State\ApiRequestStateHandler
    {
        $state = $this->getAttribute(\WHMCS\Api\NG\Versions\V2\State\ApiRequestStateHandler::REQUEST_ATTRIBUTE_NAME);
        if(!$state instanceof \WHMCS\Api\NG\Versions\V2\State\ApiRequestStateHandler) {
            throw new \WHMCS\Exception\Api\NG\ApiNgException("The system could not find the API state.  Request processing may not have used API middleware.");
        }
        return $state;
    }
    public function parseJson()
    {
        if(is_null($this->jsonAttributes)) {
            $this->jsonAttributes = json_decode($this->getBody()->getContents(), true) ?? [];
        }
    }
    public function getFromJson(string $attributeName, $default = NULL)
    {
        return \Illuminate\Support\Arr::get($this->jsonAttributes, $attributeName, $default);
    }
}

?>