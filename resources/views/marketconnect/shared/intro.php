<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<a\n    href=\"marketconnect.php?action=introVideo\"\n    class=\"btn btn-default open-modal\"\n    data-modal-title=\"";
echo AdminLang::trans("marketConnect.introModalTitle");
echo "\"\n    data-modal-size=\"modal-lg\"\n    data-modal-class=\"introVideo\"\n    id=\"playIntroVideo\"\n>\n    <i class=\"fas fa-play-circle fa-fw\"></i>\n    Watch the Video Again\n</a>\n\n<script type=\"text/javascript\">\n    jQuery(document).ready(function() {\n        var showIntro = ";
echo WHMCS\Input\Sanitize::escapeSingleQuotedString($showIntroVideo);
echo ";\n\n        jQuery('#modalAjax').on('shown.bs.modal', function(e) {\n            if (jQuery('#modalAjax').hasClass('introVideo')) {\n                jQuery('#modalAjax').on('hidden.bs.modal', function (e) {\n                    tour.start(";
if($forceTour) {
    echo "true";
}
echo ");\n                });\n            }\n        });\n\n        if (showIntro) {\n            jQuery('#playIntroVideo').trigger('click');\n        }\n    });\n</script>";

?>