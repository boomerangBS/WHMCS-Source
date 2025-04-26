<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div class=\"tablebg\">\n    <table id=\"scheduledActionsList\"\n           class=\"table display data-driven\"\n           data-ajax-url=\"";
echo routePath("admin-table-ticket-actions", $ticket->id);
echo "\"\n           data-ordering=\"false\"\n           data-defer-loading=\"0\"\n           data-server-side=\"true\"\n           data-lang-empty-table=\"";
echo AdminLang::trans("global.norecordsfound");
echo "\"\n           data-lang-zero-records=\"";
echo AdminLang::trans("global.norecordsfound");
echo "\"\n    >\n        <thead>\n        <tr>\n            <th data-name=\"actionName\" class=\"name\">";
echo AdminLang::trans("support.ticket.action.fields.action");
echo "</th>\n            <th data-name=\"actionDetail\">";
echo AdminLang::trans("support.ticket.action.fields.details");
echo "</th>\n            <th data-name=\"scheduled\">";
echo AdminLang::trans("support.ticket.action.fields.executionTime");
echo "</th>\n            <th data-name=\"createdAdmin\">";
echo AdminLang::trans("support.ticket.action.fields.scheduledBy");
echo "</th>\n            <th data-name=\"status\">";
echo AdminLang::trans("fields.status");
echo "</th>\n            <th data-name=\"edit\"></th>\n        </tr>\n        </thead>\n        <tbody>\n        ";
foreach ($scheduledActionsOfTicket as $item) {
    echo "        <tr id=\"scheduledAction";
    echo $item["id"];
    echo "\">\n            <td>";
    echo $item["actionName"];
    echo "</td>\n            <td>";
    echo $item["actionDetail"];
    echo "</td>\n            <td>";
    echo $item["scheduled"];
    echo "</td>\n            <td>";
    echo $item["createdAdmin"];
    echo "</td>\n            <td><i class=\"fas ";
    echo $item["statusIcon"];
    echo "\"></i>&nbsp;";
    echo $item["actionStatus"];
    echo "</td>\n            <td><a href=\"#\" data-action-id=\"";
    echo $item["id"];
    echo "\"><i class=\"fal ";
    echo $item["editIcon"];
    echo "\"></i></a></td>\n        </tr>\n        ";
}
echo "        </tbody>\n    </table>\n</div>\n";

?>