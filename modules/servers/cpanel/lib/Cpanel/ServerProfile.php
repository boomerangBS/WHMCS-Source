<?php

namespace WHMCS\Module\Server\Cpanel\Cpanel;

class ServerProfile
{
    protected $code = "";
    protected $name = "";
    protected $description = "";
    const SERVER_PROFILE = "STANDARD";
    const SERVER_PROFILE_CACHE_MINUTES = 60;
    public function __construct(string $code, string $name, string $description)
    {
        $this->code = $code;
        $this->name = $name;
        $this->description = $description;
    }
    public function getCode()
    {
        return $this->code;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function isProfileValid($profile)
    {
        return strcasecmp($this->code, $profile) == 0;
    }
    public static function factory($code, string $name, string $description) : \self
    {
        return new ServerProfile($code, $name, $description);
    }
    public function assertValidProfile($profile)
    {
        if(empty($this->code)) {
            return false;
        }
        if($this->isProfileValid($profile)) {
            return true;
        }
        throw new \WHMCS\Exception\Module\NotServicable($this->getTranslatedErrorMessage());
    }
    public function toArray() : array
    {
        return ["code" => $this->getCode(), "name" => $this->getName(), "description" => $this->getDescription()];
    }
    public function getTranslatedErrorMessage()
    {
        return \AdminLang::trans("configservers.invalidProfile.cpanel");
    }
}

?>