<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class KeyData
{
    protected $flag = 0;
    protected $protocol = 0;
    protected $alg = 0;
    protected $pubKey = "";
    public function __construct(int $flag, int $protocol, int $alg, string $pubKey)
    {
        $this->flag = $flag;
        $this->protocol = $protocol;
        $this->alg = $alg;
        $this->pubKey = $pubKey;
    }
    public function getFlag() : int
    {
        return $this->flag;
    }
    public function getProtocol() : int
    {
        return $this->protocol;
    }
    public function getAlg() : int
    {
        return $this->alg;
    }
    public function getPubKey()
    {
        return $this->pubKey;
    }
}

?>