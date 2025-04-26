<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
$getSortedAvailableActions = function ($availableActions, $actionsList) {
    return collect($availableActions)->sort(function (string $a, string $b) use($actionsList) {
        return $actionsList->compareActionPriority($a, $b);
    });
};
$mapActionsToOptionAttributes = function ($sortedAvailableActions, $actionsList) {
    return $sortedAvailableActions->map(function (string $class, string $name) {
        static $actionsList = NULL;
        $action = new $class();
        return ["text" => $action->displayName(), "value" => sprintf("div%s", $action::$name), "data-order" => $actionsList->getActionPriority($class)];
    });
};
$actionsList = new WHMCS\Support\Ticket\Actions\ActionsList();
$availableActionOptions = $this->selectOptionsWithAttributes($mapActionsToOptionAttributes($getSortedAvailableActions($availableActions, $actionsList), $actionsList), NULL);
echo "<div id=\"ticketScheduledActions\" class=\"panel panel-scheduled-actions-management\">\n    <div class=\"panel-heading\">\n        <div class=\"panel-title-area\">\n            <div class=\"panel-title-generic\">\n                <span class=\"panel-icon\"><i class=\"fal fa-calendar\" aria-hidden=\"true\"></i></span>\n                <span class=\"panel-title\">";
echo AdminLang::trans("support.ticketactions");
echo "</span>\n            </div>\n            <div class=\"panel-title-detailed\">\n                <div>";
echo AdminLang::trans("support.ticket.action.fields.action");
echo " <span class=\"title-scheduled-action-status\"></span>:</div>\n                <span class=\"title-scheduled-action-datetime\"></span>\n            </div>\n        </div>\n        <div class=\"panel-control-area\">\n            <button class=\"btn btn-default btn-scheduled-actions-secondary btn-scheduled-actions-cancel\">\n                <i class=\"fal fa-trash-alt\" aria-hidden=\"true\"></i>";
echo AdminLang::trans("global.cancel");
echo "            </button>\n            <button class=\"btn btn-default btn-scheduled-actions-secondary btn-scheduled-actions-close\">\n                <i class=\"fal fa-times\" aria-hidden=\"true\"></i>";
echo AdminLang::trans("global.close");
echo "            </button>\n            <button class=\"btn btn-default btn-scheduled-actions-secondary btn-scheduled-actions-discard\">\n                <i class=\"fal fa-trash-alt\" aria-hidden=\"true\"></i>";
echo AdminLang::trans("global.discard");
echo "            </button>\n            <button class=\"btn btn-default btn-scheduled-actions-primary btn-scheduled-actions-save\">\n                <i class=\"fal fa-check\" aria-hidden=\"true\"></i>";
echo AdminLang::trans("global.save");
echo "            </button>\n        </div>\n    </div>\n    <div class=\"panel-body\">\n        <div class=\"container-fluid container-created-actions\">\n            <div class=\"row container-item container-heading\">\n                <div class=\"col-xs-4 container-heading-item\">";
echo AdminLang::trans("support.ticket.action.action");
echo "</div>\n                <div class=\"col-xs-8 container-heading-item\">";
echo AdminLang::trans("support.ticket.action.details");
echo "</div>\n            </div>\n            <div class=\"row container-item container-add-action\">\n                <div class=\"col-xs-4\">\n                    <select class=\"form-control scheduled-actions-dropdown dropdown-highlight select-scheduled-actions-action-type\">\n                        ";
echo $availableActionOptions;
echo "                    </select>\n                    <button id=\"btnAddScheduledAction\" class=\"btn btn-default btn-scheduled-actions-white btn-scheduled-action-add\">\n                        <i class=\"fal fa-plus\" aria-hidden=\"true\"></i>";
echo AdminLang::trans("support.ticket.action.add");
echo "                    </button>\n                </div>\n            </div>\n        </div>\n        <div class=\"container-fluid container-time-selection\">\n            <div class=\"row container-item container-heading\">\n                <div class=\"col-xs-4  container-heading-item\">";
echo AdminLang::trans("support.ticket.action.scheduleaction");
echo "</div>\n                <div class=\"col-xs-8  container-heading-item\">";
echo AdminLang::trans("support.ticket.action.selecttime");
echo "</div>\n            </div>\n            <div class=\"row container-item\">\n                <div class=\"col-xs-4\">\n                    <select name=\"whentoaction\"\n                            class=\"form-control scheduled-actions-dropdown select-scheduled-actions-when-to-action\">\n                        <option value=\"delay\">";
echo AdminLang::trans("support.ticket.action.delay");
echo "</option>\n                        <option value=\"exact\">";
echo AdminLang::trans("support.ticket.action.exactdatetime");
echo "</option>\n                    </select>\n                </div>\n                <div class=\"col-xs-8 form-inline container-delay-inputs\">\n                    <div class=\"form-group\">\n                        <span>\n                            <input type=\"number\"\n                                   name=\"delaydays\"\n                                   min=\"0\"\n                                   value=\"0\"\n                                   class=\"form-control scheduled-actions-input\"\n                            /><label>";
echo AdminLang::trans("dateTime.days");
echo "</label>\n                        </span>\n                        <span>\n                            <input type=\"number\"\n                                   name=\"delayhours\"\n                                   min=\"0\"\n                                   value=\"0\"\n                                   class=\"form-control scheduled-actions-input\"\n                            /><label>";
echo AdminLang::trans("dateTime.hours");
echo "</label>\n                        </span>\n                        <span>\n                            <input type=\"number\"\n                                   name=\"delayminutes\"\n                                   min=\"0\"\n                                   value=\"0\"\n                                   class=\"form-control scheduled-actions-input\"\n                            /><label>";
echo AdminLang::trans("dateTime.minutes");
echo "</label>\n                        </span>\n                    </div>\n                </div>\n                <div class=\"col-xs-8 container-exact-date-time\" style=\"display: none;\">\n                    <div class=\"form-group date-picker-prepend-icon\">\n                        <label for=\"inputInvoiceDate\" class=\"field-icon\">\n                            <i class=\"fal fa-calendar-alt\"></i>\n                        </label>\n                        <input type=\"text\"\n                               name=\"exactdatetime\"\n                               class=\"form-control date-picker-single time future scheduled-actions-input\"\n                               data-opens=\"left\"\n                               disabled=\"disabled\"\n                        />\n                    </div>\n                </div>\n            </div>\n        </div>\n    </div>\n</div>\n<div class=\"modal fade modal-scheduled-action-discard-confirm\" tabindex=\"-1\" role=\"dialog\">\n    <div class=\"modal-dialog\" role=\"document\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"";
echo AdminLang::trans("global.close");
echo "\"><span aria-hidden=\"true\">&times;</span></button>\n                <h4 class=\"modal-title\">";
echo AdminLang::trans("global.unsaved.title");
echo "</h4>\n            </div>\n            <div class=\"modal-body\">\n                ";
echo AdminLang::trans("global.unsaved.confirm");
echo "            </div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">";
echo AdminLang::trans("global.goback");
echo "</button>\n                <button type=\"button\" class=\"btn btn-primary btn-scheduled-actions-discard-modal-continue\">";
echo AdminLang::trans("global.discard");
echo "</button>\n            </div>\n        </div>\n    </div>\n</div>\n<div class=\"modal fade modal-scheduled-action-cancel-confirm\" tabindex=\"-1\" role=\"dialog\">\n    <div class=\"modal-dialog\" role=\"document\">\n        <div class=\"modal-content\">\n            <div class=\"modal-header\">\n                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"";
echo AdminLang::trans("global.close");
echo "\"><span aria-hidden=\"true\">&times;</span></button>\n                <h4 class=\"modal-title\">";
echo AdminLang::trans("global.areYouSure");
echo "</h4>\n            </div>\n            <div class=\"modal-body\">";
echo AdminLang::trans("support.ticket.action.confirmCancel");
echo "</div>\n            <div class=\"modal-footer\">\n                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">";
echo AdminLang::trans("global.goback");
echo "</button>\n                <button type=\"button\" class=\"btn btn-primary btn-scheduled-actions-cancel-modal-continue\">";
echo AdminLang::trans("global.cancel");
echo "</button>\n            </div>\n        </div>\n    </div>\n</div>";

?>