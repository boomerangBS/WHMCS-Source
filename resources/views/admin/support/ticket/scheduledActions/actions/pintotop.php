<?php

$actionContainer->layout($this);
echo "<div>\n    <input name=\"parameters[";
echo $actionName;
echo "][pinToTop]\" type=\"hidden\" value=\"1\">\n</div>\n";

?>