<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Admin\ApplicationSupport\View\Traits;

// Decoded file for php version 72.
class _obfuscated_636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F41646D696E2F4170706C69636174696F6E537570706F72742F566965772F5472616974732F4A61766173637269707454726169742E7068703078376664353934323438366537_
{
    public $url;
    public $remote;
    public $noVersion;
}
trait JavascriptTrait
{
    protected $jquery = [];
    protected $javascript = [];
    protected $javascriptChart = [];
    protected $javascriptResources = [];
    public function addJquery($code)
    {
        if(!is_array($code)) {
            $code = [$code];
        }
        $this->setJquery(array_merge($this->getJquery(), $code));
        return $this;
    }
    public function addJavascript($code)
    {
        if(!is_array($code)) {
            $code = [$code];
        }
        $this->setJavascript(array_merge($this->getJavascript(), $code));
        return $this;
    }
    public function addJavascriptChart($name)
    {
        if(!is_array($name)) {
            $name = [$name];
        }
        $this->setJavascriptChart(array_merge($this->getJavascriptChart(), $name));
        return $this;
    }
    public function addJavascriptResource($url, $remote = false, $noVersion) : \self
    {
        $o = new func_num_args();
        $o->url = $url;
        $o->remote = $remote;
        $o->noVersion = $noVersion;
        $this->javascriptResources[] = $o;
        return $this;
    }
    public function getFormattedJquery()
    {
        return implode("\n", $this->getJquery());
    }
    public function getFormattedJavascript()
    {
        return implode("\n", $this->getJavascript()) . "\n" . $this->getChartRedrawJavascript();
    }
    protected function getChartRedrawJavascript()
    {
        $redraw = "function redrawCharts() { ";
        foreach ($this->getJavascriptChart() as $chart) {
            $redraw .= $chart . "();\n";
        }
        $redraw .= "}\n";
        return $redraw . "\$(window).bind(\"resize\", function(event) { redrawCharts(); });";
    }
    public function getJquery()
    {
        return $this->jquery;
    }
    public function getFormattedJavascriptResources($versionHash)
    {
        $markup = "";
        foreach ($this->javascriptResources as $resource) {
            $markup .= $this->getMarkupForJavascriptResource($resource, $versionHash);
            $markup .= "\n";
        }
        return $markup;
    }
    public function getMarkupForJavascriptResource($resource, string $versionHash)
    {
        $url = $resource->url;
        if($versionHash != "" && !$resource->remote && !$resource->noVersion) {
            $url .= "?v=" . $versionHash;
        }
        return sprintf("<script defer type=\"text/javascript\" src=\"%s\"></script>", $url);
    }
    public function setJquery($jquery)
    {
        $this->jquery = $jquery;
        return $this;
    }
    public function getJavascript()
    {
        return $this->javascript;
    }
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;
        return $this;
    }
    public function getJavascriptChart()
    {
        return $this->javascriptChart;
    }
    public function setJavascriptChart($javascriptChart)
    {
        $this->javascriptChart = $javascriptChart;
        return $this;
    }
    public function getJavascriptResources() : array
    {
        return $this->javascriptResources;
    }
    public function modal($name, $title, $message, array $buttons = [], $size = "", $panelType = "primary")
    {
        switch ($size) {
            case "small":
                $dialogClass = "modal-dialog modal-sm";
                break;
            case "large":
                $dialogClass = "modal-dialog modal-lg";
                break;
            default:
                $dialogClass = "modal-dialog";
                switch ($panelType) {
                    case "default":
                    case "primary":
                    case "success":
                    case "info":
                    case "warning":
                    case "danger":
                        $panel = "panel-" . $panelType;
                        break;
                    default:
                        $panel = "panel-primary";
                        $buttonsOutput = "";
                        foreach ($buttons as $button) {
                            $id = \WHMCS\View\Helper::generateCssFriendlyId($name, $button["title"]);
                            $onClick = isset($button["onclick"]) ? "onclick='" . $button["onclick"] . "'" : "data-dismiss=\"modal\"";
                            $class = isset($button["class"]) ? $button["class"] : "btn-default";
                            $type = isset($button["type"]) ? $button["type"] : "button";
                            $buttonsOutput .= "<button type=\"" . $type . "\" id=\"" . $id . "\" class=\"btn " . $class . "\" " . $onClick . ">\n    " . $button["title"] . "\n</button>";
                        }
                        $modalOutput = "<div class=\"modal fade\" id=\"modal" . $name . "\" role=\"dialog\" aria-labelledby=\"" . $name . "Label\" aria-hidden=\"true\">\n    <div class=\"" . $dialogClass . "\">\n        <div class=\"modal-content panel " . $panel . "\">\n            <div id=\"modal" . $name . "Heading\" class=\"modal-header panel-heading\">\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">\n                    <span aria-hidden=\"true\">&times;</span>\n                    <span class=\"sr-only\">{AdminLang::trans('global.close')}</span>\n                </button>\n                <h4 class=\"modal-title\" id=\"" . $name . "Label\">" . $title . "</h4>\n            </div>\n            <div id=\"modal" . $name . "Body\" class=\"modal-body panel-body\">\n                " . $message . "\n            </div>\n            <div id=\"modal" . $name . "Footer\" class=\"modal-footer panel-footer\">\n                " . $buttonsOutput . "\n            </div>\n        </div>\n    </div>\n</div>";
                        return $modalOutput;
                }
        }
    }
    public function modalWithConfirmation($name, $question, $url)
    {
        $modalOutput = \WHMCS\View\Helper::confirmationModal($name, $question, $url);
        $js = "function " . $name . "(id) {\n    \$('#" . $name . "').find('.id-target').val(id).end().modal('show');\n}";
        $this->addJavascript($js);
        return $modalOutput;
    }
}

?>