<?php

echo "\n<div>\n    <a href=\"";
echo $action->getUrl();
echo "\"\n       target=\"_blank\" class=\"btn btn-info btn-sm pull-right btn-promotion-action\"\n    >\n        ";
echo $action->getText();
echo "    </a>\n</div>\n";

?>