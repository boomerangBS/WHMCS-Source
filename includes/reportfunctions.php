<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
function addPrintInputToForm($formContainer)
{
    return preg_replace("/(<form\\W[^>]*\\bmethod=('|\"|)POST('|\"|)\\b[^>]*>)/i", "\\1\n<input type=\"hidden\" name=\"print\" value=\"true\" />", $formContainer);
}
function getReportsList()
{
    static $textReports = NULL;
    if(!$textReports) {
        $textReports = [];
        $reportDir = ROOTDIR . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "reports" . DIRECTORY_SEPARATOR;
        $dh = opendir($reportDir);
        while (false !== ($file = readdir($dh))) {
            if($file != "index.php" && is_file($reportDir . $file)) {
                $file = str_replace(".php", "", $file);
                if(substr($file, 0, 5) != "graph") {
                    $niceName = str_replace("_", " ", $file);
                    $niceName = titleCase($niceName);
                    $textReports[$file] = $niceName;
                }
            }
        }
        closedir($dh);
        asort($textReports);
    }
    return $textReports;
}

?>