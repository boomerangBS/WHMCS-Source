<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic\Api;

abstract class AbstractApi implements ApiInterface
{
    protected $url;
    protected $username;
    protected $password;
    protected $parser;
    protected $proxy;
    protected $transport;
    public function __construct(string $url, string $username, string $password, ParserInterface $parser, TransportInterface $transport)
    {
        $this->url = trim($url);
        $this->username = trim($username);
        $this->password = trim($password);
        $this->parser = $parser;
        $this->transport = $transport;
    }
    public function getParser() : ParserInterface
    {
        return $this->parser;
    }
    public function getUrl()
    {
        return $this->url ?? "";
    }
    public function getUsername()
    {
        return $this->username ?? "";
    }
    public function getPassword()
    {
        return $this->password ?? "";
    }
    public function getTransport() : TransportInterface
    {
        return $this->transport;
    }
    public function call(\WHMCS\Module\Registrar\CentralNic\Commands\AbstractCommand $command) : Response
    {
        try {
            return new Response($this->parser, $this->doCall($command));
        } catch (\Exception $e) {
            throw new \Exception("Remote Provider Error: " . $e->getMessage(), $e->getCode(), $e);
        }
    }
    public function setProxy($proxy) : \self
    {
        $this->proxy = $proxy;
        return $this;
    }
    public function getProxy()
    {
        return $this->proxy ?? "";
    }
    public function doCall(\WHMCS\Module\Registrar\CentralNic\Commands\AbstractCommand $command) : \WHMCS\Module\Registrar\CentralNic\Commands\AbstractCommand
    {
        return $this->transport->doCall($command, $this);
    }
    public abstract function getCustomHeader();
}

?>