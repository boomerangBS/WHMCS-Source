<?php

namespace WHMCS\Module\Server;

class CustomAction
{
    protected $identifier;
    protected $display;
    protected $callable;
    protected $params = [];
    protected $permissions = [];
    protected $active = true;
    protected $allowSamePage = false;
    protected $preferIsolation = false;
    public static function factory($identifier, string $display, $callable = [], array $params = [], array $permissions = true, $active = false, $allowSamePage = false, $preferIsolation) : CustomAction
    {
        $self = new static();
        return $self->setIdentifier($identifier)->setDisplay($display)->setCallable($callable)->setParams($params)->setPermissions($permissions)->setActive($active)->setAllowSamePage($allowSamePage)->setPreferIsolation($preferIsolation);
    }
    public function getIdentifier()
    {
        return $this->identifier;
    }
    protected function setIdentifier($identifier) : \self
    {
        $this->identifier = $identifier;
        return $this;
    }
    public function getDisplay()
    {
        return $this->display;
    }
    protected function setDisplay($display) : \self
    {
        $this->display = $display;
        return $this;
    }
    public function invokeCallable() : array
    {
        return call_user_func_array($this->callable, $this->params);
    }
    protected function setCallable($callable) : \self
    {
        if(!is_callable($callable)) {
            throw new \InvalidArgumentException("The provided callable must be a value that can be called as a function.");
        }
        $this->callable = $callable;
        return $this;
    }
    protected function setParams($params) : \self
    {
        $this->params = $params;
        return $this;
    }
    public function getPermissions() : array
    {
        return $this->permissions;
    }
    protected function setPermissions($permissions) : \self
    {
        $this->permissions = $permissions;
        return $this;
    }
    protected function setActive($active) : \self
    {
        $this->active = $active;
        return $this;
    }
    public function isActive()
    {
        return $this->active;
    }
    protected function setAllowSamePage($allowSamePage) : \self
    {
        $this->allowSamePage = $allowSamePage;
        return $this;
    }
    public function isAllowSamePage()
    {
        return $this->allowSamePage;
    }
    protected function setPreferIsolation($preferIsolation) : \self
    {
        $this->preferIsolation = $preferIsolation;
        return $this;
    }
    public function isPreferIsolation()
    {
        return $this->preferIsolation;
    }
}

?>