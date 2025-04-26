<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\ApplicationSupport\View\Html\Smarty;

class BodyContentWrapper extends \WHMCS\Admin\ApplicationSupport\View\Html\AbstractTemplateEngine
{
    public function __construct($data = "", $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
        $this->setBodyContent($data);
    }
    public function getFormattedBodyContent()
    {
        return $this->getBodyContent();
    }
    protected function factoryEngine()
    {
        return \DI::make("View\\Engine\\Smarty\\Admin");
    }
    public function getFormattedFooterContent()
    {
        $smarty = $this->getTemplateEngine();
        $footer_output = $smarty->fetch($this->getTemplateDirectory() . "/footer.tpl");
        $licenseBannerHtml = $this->getLicenseBannerHtml();
        if($licenseBannerHtml) {
            $footer_output = $this->appendToHtmlBody($footer_output, $licenseBannerHtml);
        }
        if(\WHMCS\Utility\MixPanel::isMixPanelTrackingEnabled()) {
            $mixPanelInitJs = \WHMCS\Utility\MixPanel::getMixpanelInitJs($this->adminUser);
            $footer_output = $this->appendToHtmlBody($footer_output, $mixPanelInitJs);
        }
        return $footer_output;
    }
    public function getFormattedHeaderContent()
    {
        $smarty = $this->getTemplateEngine();
        $smarty->assign("globalAdminWarningMsg", $this->getGlobalWarningNotification());
        $smarty->assign("clientLimitNotification", $this->getClientLimitNotification());
        return $smarty->fetch($this->getTemplateDirectory() . "/header.tpl");
    }
    protected function appendToHtmlBody($document, string $partial)
    {
        $endBodyTagPosition = strpos($document, "</body>");
        if($endBodyTagPosition === false) {
            $document .= $partial;
        } else {
            $document = substr($document, 0, $endBodyTagPosition) . $partial . substr($document, $endBodyTagPosition);
        }
        return $document;
    }
}

?>