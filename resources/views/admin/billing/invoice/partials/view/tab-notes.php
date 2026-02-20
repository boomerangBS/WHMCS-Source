<?php

echo $aInt->nextAdminTab();
if($invoice->adminNotes) {
    echo "<blockquote>" . nl2br($invoice->adminNotes) . "</blockquote>";
} else {
    echo WHMCS\View\Helper::alert("There are no notes on this invoice.", "info", "no-margin");
}

?>