<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Wizard\Steps\GettingStarted;

class Start
{
    public function getStepContent()
    {
        $mixPanelChecked = \WHMCS\Utility\MixPanel::isMixPanelTrackingEnabled() ? "checked" : "";
        return "<div class=\"wizard-transition-step\">\n    <div class=\"icon\"><i class=\"far fa-lightbulb\"></i></div>\n    <div class=\"title\">{lang key=\"wizard.welcome\"}</div>\n    <div class=\"tag\">{lang key=\"wizard.intro\"}</div>\n    <div class=\"greyout\">{lang key=\"wizard.noTime\"}</div>\n    \n    <div class=\"row\" style=\"padding: 20px 10px;\">\n        <div class=\"col-12\">\n        <div class=\"checkbox-wrapper\">\n            <input id=\"checkboxMixpanelEnable\" type=\"checkbox\" class=\"keep_default\" name=\"MixPanelTrackingEnabled\" " . $mixPanelChecked . ">\n            <label for=\"checkboxMixpanelEnable\" style=\"font-weight: 400;\">\n                {lang key=\"wizard.mixpanelCheckboxLabel\"}\n            </label>\n        </div>\n        </div>\n    </div>\n</div>";
    }
    public function save($data) : void
    {
        $isMixPanelCheckboxEnabled = \Illuminate\Support\Arr::get($data, "MixPanelTrackingEnabled") === "on";
        if($isMixPanelCheckboxEnabled !== \WHMCS\Utility\MixPanel::isMixPanelTrackingEnabled()) {
            \WHMCS\Utility\MixPanel::setMixPanelTrackingStatus($isMixPanelCheckboxEnabled);
        }
    }
}

?>