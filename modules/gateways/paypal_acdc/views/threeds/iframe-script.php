<?php

echo "\n<script type=\"application/javascript\">\n    jQuery(document).ready(function() {\n        jQuery(\"iframe[name='3dauth']\").attr('src', \"";
echo $actionURL;
echo "\");\n    });\n</script>\n";

?>