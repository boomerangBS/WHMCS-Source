<?php

namespace WHMCS\Module\Server\Plesk\Plesk;

class ErrorOverride
{
    private $defaultType;
    private $overrides = ["1013" => "WARNING_DOMAIN_NOT_EXIST"];
    private $types;
    private $error;
    const DANGER_MESSAGE_TYPE = "danger";
    const WARNING_MESSAGE_TYPE = "warning";
    public function __construct(\Throwable $error)
    {
        $this->error = $error;
        $this->defaultType = self::DANGER_MESSAGE_TYPE;
    }
    public function getMessageLangKey()
    {
        return $this->overrides[$this->error->getCode()] ?? "ERROR_COMMON_MESSAGE";
    }
    public function getMessageType()
    {
        foreach ($this->types as $type => $codes) {
            if(in_array($this->error->getCode(), $codes)) {
                return $type;
            }
        }
        return $this->defaultType;
    }
}

?>