<?php

echo "<script>\n    \$(document).ready(function() {\n        initDateRangePicker();\n        setScheduledActionTabCount(\"";
echo addslashes($numScheduledActions);
echo "\");\n        WHMCS.ui.dataTable.getTableById(\n            'scheduledActionsList',\n            {\n                dom: '',\n                paging: false,\n                lengthChange: false,\n                searching: false,\n                ordering: false,\n                info: false,\n                autoWidth: false,\n                language: {\n                    emptyTable: \"";
echo addslashes(AdminLang::trans("global.norecordsfound"));
echo "\"\n                }\n            }\n        );\n    });\n</script>\n<div class=\"container-fluid container-scheduled-actions-tab\">\n    <div class=\"clearfix\">\n        <button type=\"button\"\n                class=\"btn btn-default btns-padded btn-schedule-actions btn-scheduled-actions-manage pull-right\">\n            <i class=\"fal fa-calendar\" aria-hidden=\"true\"></i>";
echo AdminLang::trans("support.ticket.action.manageactions");
echo "        </button>\n    </div>\n    ";
echo $listActions;
echo "    <form>\n    ";
echo generate_token();
echo "    ";
echo $createAndViewActions;
echo "    </form>\n</div>\n";

?>