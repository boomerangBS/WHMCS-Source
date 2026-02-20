<?php

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