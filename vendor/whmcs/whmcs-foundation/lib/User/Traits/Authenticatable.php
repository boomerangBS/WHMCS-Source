<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Traits;

trait Authenticatable
{
    protected $authIdentifierName = "email";
    protected $secondFactorModuleName = "second_factor";
    protected $secondFactorConfigName = "second_factor_config";
    protected $rememberTokenName = "remember_token";
    public function getAuthIdentifierName()
    {
        return $this->authIdentifierName;
    }
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }
    public function getAuthPassword()
    {
        return $this->password;
    }
    public function getRememberToken()
    {
        if($this->getRememberTokenName()) {
            return (string) $this->{$this->getRememberTokenName()};
        }
        return NULL;
    }
    public function setRememberToken($value) : \self
    {
        if($this->getRememberTokenName()) {
            $this->{$this->getRememberTokenName()} = $value;
        }
        return $this;
    }
    public function getRememberTokenName()
    {
        return $this->rememberTokenName;
    }
    public function getSecondFactorModule()
    {
        if($this->getSecondFactorModuleName()) {
            return (string) $this->{$this->getSecondFactorModuleName()};
        }
        return NULL;
    }
    public function setSecondFactorModuleName($value) : \self
    {
        if($this->getSecondFactorModuleName()) {
            $this->{$this->getSecondFactorModuleName()} = $value;
        }
        return $this;
    }
    public function getSecondFactorModuleName()
    {
        return $this->secondFactorModuleName;
    }
    private function getEncryptionKey()
    {
        return hash("sha256", \DI::make("config")["cc_encryption_hash"]);
    }
    public function getSecondFactorConfig()
    {
        if($this->getSecondFactorConfigName()) {
            $config = $this->{$this->getSecondFactorConfigName()};
            if(!is_string($config) || strlen($config) == 0) {
                return [];
            }
            $wasEncrypted = false;
            if($this->isAesDecryptable($config)) {
                $wasEncrypted = true;
                $config = trim($this->aesDecryptValue($config, $this->getEncryptionKey()));
            }
            $data = json_decode($config, true);
            if(!$wasEncrypted) {
                if(is_null($data)) {
                    $data = safe_unserialize($config);
                    if(is_array($data)) {
                        $this->setSecondFactorConfig($data)->save();
                    }
                } elseif(is_array($data)) {
                    $this->setSecondFactorConfig($data)->save();
                }
            }
            if(!is_array($data)) {
                $data = [];
            }
            return $data;
        }
        return NULL;
    }
    public function setSecondFactorConfig($value) : \self
    {
        $value = empty($value) ? "" : json_encode($value);
        if($this->getSecondFactorConfigName()) {
            $value = str_pad($value, random_int(0, 16), " ");
            $padLength = 0;
            while ($padLength < strlen($value)) {
                $padLength += 32;
            }
            $this->{$this->getSecondFactorConfigName()} = $this->aesEncryptValue(str_pad($value, $padLength, " "), $this->getEncryptionKey());
        }
        return $this;
    }
    public function getSecondFactorConfigName()
    {
        return $this->secondFactorConfigName;
    }
    public function banIpAddress() : \self
    {
        \WHMCS\Database\Capsule::table("tblbannedips")->insert(["ip" => \App::getRemoteIp(), "reason" => "Login Attempts Exceeded", "expires" => \WHMCS\Carbon::now()->addMinutes(15)->toDateTimeString()]);
        return $this;
    }
    public function disableTwoFactorAuthentication() : \self
    {
        $this->setSecondFactorModuleName("");
        $this->setSecondFactorConfig([]);
        return $this;
    }
}

?>