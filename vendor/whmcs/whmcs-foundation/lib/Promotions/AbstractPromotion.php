<?php

namespace WHMCS\Promotions;

abstract class AbstractPromotion
{
    protected $view;
    protected $identifier = "";
    protected $title = "";
    protected $description = "";
    protected $action;
    protected $logoUrl;
    protected $dismissTTL = 0;
    protected $transientKeyDismissPrefix = "HidePromo";
    public abstract function isPromotable();
    public function dismiss()
    {
        if(!$this->isDismissible()) {
            return false;
        }
        return \WHMCS\TransientData::getInstance()->store($this->dismissTransientKey(), "1", $this->getDismissTTL());
    }
    public function isDismissible()
    {
        return $this->getDismissTTL() != 0;
    }
    public function getView()
    {
        return $this->view->view($this);
    }
    public function getIdentifier()
    {
        return $this->identifier;
    }
    public function setIdentifier($identifier) : \self
    {
        $this->identifier = $identifier;
        return $this;
    }
    public function getTitle()
    {
        return $this->title;
    }
    public function setTitle($title) : \self
    {
        $this->title = $title;
        return $this;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function setDescription($description) : \self
    {
        $this->description = $description;
        return $this;
    }
    public function hasAction()
    {
        return !is_null($this->action);
    }
    public function getAction() : PromotionAction
    {
        return $this->action;
    }
    public function setAction(PromotionAction $action) : \self
    {
        $this->action = $action;
        return $this;
    }
    public function setViewInstance(PromotionViewInterface $view) : \self
    {
        $this->view = $view;
        return $this;
    }
    public function getLogoUrl()
    {
        return $this->logoUrl;
    }
    public function setLogoUrl($logoUrl) : \self
    {
        $this->logoUrl = $logoUrl;
        return $this;
    }
    public function getDismissTTL() : int
    {
        return $this->dismissTTL;
    }
    public function setDismissTTL($dismissTTL) : \self
    {
        $this->dismissTTL = $dismissTTL;
        return $this;
    }
    protected function dismissTransientKey()
    {
        return $this->transientKeyDismissPrefix . $this->getIdentifier();
    }
    public function isDismissed()
    {
        return (bool) \WHMCS\TransientData::getInstance()->retrieve($this->dismissTransientKey());
    }
}

?>