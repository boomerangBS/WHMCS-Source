<?php

namespace WHMCS\Admin\ApplicationSupport\View\Html;

abstract class AbstractNoEngine extends \WHMCS\Http\Message\AbstractViewableResponse implements \WHMCS\View\HtmlPageInterface
{
    use \WHMCS\Admin\ApplicationSupport\View\Traits\AdminHtmlViewTrait;
    protected function factoryEngine()
    {
        throw new \RuntimeException("WHMCS\\Admin\\ApplicationSupport\\View\\Html\\AbstractNoEngine must not rely on WHMCS\\Admin\\ApplicationSupport\\View\\Html\\AbstractNoEngine::factoryEngine");
    }
    public function getOutputContent()
    {
        $this->prepareVariableContent();
        $hookVariables = $this->getTemplateVariables()->all();
        ob_start();
        $hookVariables = $this->runHookAdminAreaPage($hookVariables);
        $this->getTemplateVariables()->add($hookVariables);
        $this->getTemplateVariables()->add(["headoutput" => $this->runHookAdminHeadOutput($hookVariables)]);
        $this->getTemplateVariables()->add(["headeroutput" => $this->runHookAdminHeaderOutput($hookVariables)]);
        $this->getTemplateVariables()->add(["footeroutput" => $this->runHookAdminFooterOutput($hookVariables)]);
        echo $this->getFormattedHtmlHeadContent() . $this->getFormattedHeaderContent() . $this->getFormattedBodyContent();
        echo $this->getFormattedFooterContent();
        $html = ob_get_clean();
        return (new \WHMCS\Admin\ApplicationSupport\View\PreRenderProcessor())->process($html);
    }
    public function getFormattedHtmlHeadContent()
    {
        $pageDateFormat = $this->getTemplateVariables()->get("datepickerformat");
        if(!$pageDateFormat) {
            $pageDateFormat = $this->getDateFormat();
        }
        $javascript = $this->getFormattedJavascript();
        $jQuery = $this->getFormattedJquery();
        $javascriptResources = $this->getFormattedJavascriptResources($this->getVersionHash());
        $headElements = implode("\n", $this->getHtmlHeadElements()) . $this->getTemplateVariables()->get("headoutput", "");
        $whmcsBaseUrl = \WHMCS\Utility\Environment\WebHelper::getBaseUrl();
        $adminBaseRoutePath = \WHMCS\Admin\AdminServiceProvider::getAdminRouteBase();
        $fontCssInclude = \DI::make("asset")->fontCssInclude("open-sans-family.css");
        $html = "<!DOCTYPE html>\n<html lang=\"en\">\n  <head>\n    <meta charset=\"" . $this->getCharset() . "\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n\n    <title>WHMCS - " . $this->getTitle() . "</title>\n\n    " . $fontCssInclude . "\n    <link href=\"templates/" . $this->getTemplateDirectory() . "/css/all.min.css?v=" . $this->getVersionHash() . "\" rel=\"stylesheet\" />\n    <link href=\"templates/" . $this->getTemplateDirectory() . "/css/theme.min.css?v=" . $this->getVersionHash() . "\" rel=\"stylesheet\" />\n    <script type=\"text/javascript\" src=\"templates/" . $this->getTemplateDirectory() . "/js/vendor.min.js?v=" . $this->getVersionHash() . "\"></script>\n    <script type=\"text/javascript\" src=\"templates/" . $this->getTemplateDirectory() . "/js/scripts.min.js?v=" . $this->getVersionHash() . "\"></script>\n    " . $javascriptResources . "\n    <script>\n        var datepickerformat = \"" . $pageDateFormat . "\",\n            csrfToken=\"" . $this->getCsrfToken() . "\";\n            adminBaseRoutePath = \"" . $adminBaseRoutePath . "\";\n            whmcsBaseUrl = \"" . $whmcsBaseUrl . "\";\n            \$(document).ready(function(){\n                " . $jQuery . "\n            });\n            " . $javascript . "\n    </script>\n    " . $headElements . "\n</head>";
        return $html;
    }
    public function getFormattedHeaderContent()
    {
        return "<body>" . PHP_EOL . $this->getTemplateVariables()->get("headeroutput", "");
    }
    public function getFormattedFooterContent()
    {
        return $this->getTemplateVariables()->get("footeroutput", "") . "</body>" . PHP_EOL . "</html>";
    }
    public function getFormattedBodyContent()
    {
        return $this->getBodyContent();
    }
}

?>