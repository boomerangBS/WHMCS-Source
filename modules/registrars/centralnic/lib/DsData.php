<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Registrar\CentralNic;

class DsData
{
    protected $keyTag = 0;
    protected $alg = 0;
    protected $digestType = 0;
    protected $digest = "";
    public function __construct(int $keyTag, int $alg, int $digestType, string $digest)
    {
        $this->keyTag = $keyTag;
        $this->alg = $alg;
        $this->digestType = $digestType;
        $this->digest = $digest;
    }
    public function getKeyTag() : int
    {
        return $this->keyTag;
    }
    public function getAlg() : int
    {
        return $this->alg;
    }
    public function getDigestType() : int
    {
        return $this->digestType;
    }
    public function getDigest()
    {
        return $this->digest;
    }
}

?>