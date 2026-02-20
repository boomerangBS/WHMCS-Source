<?php

echo "<div class=\"row container-item container-scheduled-action cloneable div";
echo $actionName;
echo "\"\n     data-value=\"div";
echo $actionName;
echo "\"\n     data-order=\"";
echo $actionPriority;
echo "\">\n    <div class=\"col-xs-4\">\n        <input type=\"hidden\" name=\"actions[]\" value=\"";
echo $actionName;
echo "\" disabled=\"disabled\">\n        <span></span>\n    </div>\n    <div class=\"col-xs-8\">\n        ";
echo $this->section("content");
echo "        <div>\n            <button class=\"btn btn-default btn-scheduled-actions-white btn-scheduled-action-cancel\">\n                <i class=\"fal fa-times\" title=\"";
echo AdminLang::trans("global.cancel");
echo "\"></i>\n            </button>\n        </div>\n    </div>\n</div>\n\n";

?>