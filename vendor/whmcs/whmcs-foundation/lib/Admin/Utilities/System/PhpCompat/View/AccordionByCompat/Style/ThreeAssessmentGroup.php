<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Utilities\System\PhpCompat\View\AccordionByCompat\Style;

class ThreeAssessmentGroup
{
    protected $assessmentGroups = [];
    public function __construct()
    {
        $this->assessmentGroups = $this->defaultAssessmentGroups();
    }
    public function defaultAssessmentGroups($phpVersionId = NULL)
    {
        return [\WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO => ["type" => "Incompat", "desc" => \AdminLang::trans("phpCompatUtil.compatNoDesc"), "title" => \AdminLang::trans("phpCompatUtil.compatNoTitle"), "titleCssClass" => "default", "data" => []], \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_UNLIKELY => ["type" => "Unknown", "desc" => \AdminLang::trans("phpCompatUtil.compatUnknownDesc"), "title" => \AdminLang::trans("phpCompatUtil.compatUnknownTitle"), "titleCssClass" => "default", "data" => []], \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES => ["type" => "Compat", "desc" => \AdminLang::trans("phpCompatUtil.compatYesDesc"), "title" => \AdminLang::trans("phpCompatUtil.compatYesTitle"), "titleCssClass" => "success", "data" => []]];
    }
}

?>