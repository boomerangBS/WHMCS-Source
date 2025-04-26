<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<script type=\"application/javascript\">\njQuery(document).ready(function() {\n    var payMethodEditForm = jQuery('#frmManagePaymentMethod');\n    if (payMethodEditForm.length == 1) {\n                var ambiguousContainer = payMethodEditForm.find('div.fieldgroup-auxfields .submit-container')\n            .closest('div.fieldgroup-auxfields');\n        ambiguousContainer.children()\n            .not('.submit-container')\n            .hide()            .addClass('hidden');        var elements = ambiguousContainer;\n        elements = elements.add(\n                payMethodEditForm.find('div.fieldgroup-auxfields input[id=\"inputDescription\"]')\n                    .closest('div.fieldgroup-auxfields')\n            );\n        elements.show();        elements.removeClass('hidden');    }\n});\n</script>\n";

?>