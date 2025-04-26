<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Wizard;

class GettingStarted extends Wizard
{
    protected $wizardName = "GettingStarted";
    public function __construct()
    {
        $this->steps = [["name" => "Start", "hidden" => true], ["name" => "Settings", "stepName" => \AdminLang::trans("wizard.stepGeneral"), "stepDescription" => \AdminLang::trans("wizard.stepGeneralDesc")], ["name" => "Payments", "stepName" => \AdminLang::trans("wizard.stepPayments"), "stepDescription" => \AdminLang::trans("wizard.stepPaymentsDesc")], ["name" => "Registrars", "stepName" => \AdminLang::trans("wizard.stepDomains"), "stepDescription" => \AdminLang::trans("wizard.stepDomainsDesc")], ["name" => "Enom", "stepName" => \AdminLang::trans("wizard.stepEnom"), "stepDescription" => \AdminLang::trans("wizard.stepEnomDesc"), "hidden" => true], ["name" => "Servers", "stepName" => \AdminLang::trans("wizard.stepWebHosting"), "stepDescription" => \AdminLang::trans("wizard.stepWebHostingDesc")], ["name" => "MarketConnect", "stepName" => \AdminLang::trans("wizard.stepAddonsExtras"), "stepDescription" => \AdminLang::trans("wizard.stepAddonsExtrasDescription")], ["name" => "Complete", "hidden" => true, "postSaveEvent" => function () {
            \WHMCS\Config\Setting::setValue("DisableSetupWizard", 1);
        }]];
    }
    public function hasRequiredAdminPermissions()
    {
        return \WHMCS\User\Admin\Permission::currentAdminHasPermissionName("Configure General Settings");
    }
}

?>