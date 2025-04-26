<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
echo "<div class=\"panel panel-default\">\n    <div class=\"panel-heading\">\n        <h2 class=\"popup-header-padding\"><i class='far fa-envelope'>&nbsp;</i>";
echo $subject;
echo "</h2>\n        <div><b>";
echo AdminLang::trans("emails.to");
echo "</b>: ";
echo $to;
echo "</div>\n        ";
if($cc) {
    echo "            <div><b>";
    echo AdminLang::trans("emails.cc");
    echo "</b>: ";
    echo $cc;
    echo "</div>\n            ";
}
if($bcc) {
    echo "            <div><b>";
    echo AdminLang::trans("emails.bcc");
    echo "</b>: ";
    echo $bcc;
    echo "</div>\n        ";
}
if(0 < count($attachments)) {
    echo "            <div class=\"popup-header-padding\">\n                ";
    foreach ($attachments as $index => $attachedFile) {
        echo "                    <i class=\"fal fa-paperclip\"></i> ";
        echo $attachedFile;
        echo "                    ";
        echo $index + 1 !== count($attachments) ? "<br>" : "";
        echo "                ";
    }
    echo "            </div>\n        ";
}
echo "    </div>\n    <div class=\"panel-body main-content\">\n        <iframe id=\"emailContent\" width=\"100%\" height=\"300\" frameborder=\"0\" srcdoc=\"";
echo escape($message);
echo "\"></iframe>\n    </div>\n</div>\n";

?>