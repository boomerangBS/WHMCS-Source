<?php

echo "\n<div>\n    <a href=\"";
echo $action->getUrl();
echo "\"\n       class=\"btn btn-info btn-sm pull-right btn-promotion-action app-inner open-modal\"\n       data-modal-class=\"app-info-modal\" data-modal-size=\"modal-lg\"\n    >\n        ";
echo $action->getText();
echo "    </a>\n</div>\n";

?>