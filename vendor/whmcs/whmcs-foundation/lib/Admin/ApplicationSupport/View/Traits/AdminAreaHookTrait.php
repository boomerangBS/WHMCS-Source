<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Admin\ApplicationSupport\View\Traits;

// Decoded file for php version 72.
trait AdminAreaHookTrait
{
    public function runHookAdminFooterOutput(array $hookVariables)
    {
        $hookResult = run_hook("AdminAreaFooterOutput", $hookVariables);
        $hookResult[] = view("admin.utilities.date.footer");
        return count($hookResult) ? implode("\n", $hookResult) : "";
    }
    public function runHookAdminHeaderOutput(array $hookVariables)
    {
        $hookResult = run_hook("AdminAreaHeaderOutput", $hookVariables);
        return count($hookResult) ? implode("\n", $hookResult) : "";
    }
    public function runHookAdminHeadOutput(array $hookVariables)
    {
        $hookResult = run_hook("AdminAreaHeadOutput", $hookVariables);
        return count($hookResult) ? implode("\n", $hookResult) : "";
    }
    public function runHookAdminAreaPage(array $hookVariables)
    {
        $hookResult = run_hook("AdminAreaPage", $hookVariables);
        foreach ($hookResult as $arr) {
            foreach ($arr as $k => $v) {
                $hookVariables[$k] = $v;
            }
        }
        return $hookVariables;
    }
}

?>