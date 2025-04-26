<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Commands;

abstract class AbstractCommand
{
    use \WHMCS\Module\Registrar\CentralNic\ParametersTrait;
    protected $api;
    protected $httpMethod = "POST";
    protected $params = [];
    public function __construct(\WHMCS\Module\Registrar\CentralNic\Api\ApiInterface $api)
    {
        $this->api = $api;
    }
    public function getCommand()
    {
        if(empty($this->command)) {
            throw new \Exception("Command can not be empty");
        }
        return $this->command;
    }
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }
    public function setParam($key, $value) : \self
    {
        $this->params[trim($key)] = $value;
        return $this;
    }
    public function addParams($params) : \self
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }
    public function getParams() : array
    {
        return $this->params;
    }
    public function deleteParam($key) : \self
    {
        unset($this->params[$key]);
        return $this;
    }
    public function execute() : \WHMCS\Module\Registrar\CentralNic\Api\Response
    {
        $this->params = array_merge(["command" => $this->getCommand()], $this->params);
        return $this->handleResponse($this->api->call($this));
    }
    public function handleResponse(\WHMCS\Module\Registrar\CentralNic\Api\Response $response) : \WHMCS\Module\Registrar\CentralNic\Api\Response
    {
        $response->getCode();
        switch ($response->getCode()) {
            case "200":
                return $response;
                break;
            default:
                throw new \Exception($response->getDescription(), $response->getCode());
        }
    }
}

?>