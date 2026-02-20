<?php

namespace WHMCS\Promotions;

class PromotionAction
{
    protected $url = "";
    protected $text = "";
    protected $actionViewer = "admin.promotions.actionViewer";
    public function __construct(string $url, string $langKey)
    {
        $this->url = $url;
        $this->text = \AdminLang::trans($langKey);
    }
    public function asModal() : \self
    {
        $this->setViewer("admin.promotions.actionViewerAsModal");
        return $this;
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function setUrl($url) : \self
    {
        $this->url = $url;
        return $this;
    }
    public function getText()
    {
        return $this->text;
    }
    public function setText($text) : \self
    {
        $this->text = $text;
        return $this;
    }
    public function getActionViewer()
    {
        return $this->actionViewer;
    }
    public function setViewer($viewer) : \self
    {
        $this->actionViewer = $viewer;
        return $this;
    }
    public function view()
    {
        return view($this->actionViewer, ["action" => $this]);
    }
}

?>