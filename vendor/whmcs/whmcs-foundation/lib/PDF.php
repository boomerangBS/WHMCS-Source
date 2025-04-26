<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS;

// Decoded file for php version 72.
define("K_TCPDF_EXTERNAL_CONFIG", true);
define("K_PATH_CACHE", \Config::self()->templates_compiledir . DIRECTORY_SEPARATOR);
define("PDF_CREATOR", "WHMCS");
define("PDF_AUTHOR", "WHMCS");
define("PDF_HEADER_TITLE", "");
define("PDF_HEADER_STRING", "");
define("PDF_MARGIN_FOOTER", 15);
define("PDF_MARGIN_TOP", 25);
class PDF extends \TCPDF
{
    protected $paperSize;
    protected $headerTplFile = "";
    protected $footerTplFile = "";
    protected $templateVars = [];
    public function __construct()
    {
        $unicode = strtolower(substr(Config\Setting::getValue("Charset"), 0, 3)) != "iso";
        $paperSize = Config\Setting::getValue("PDFPaperSize");
        if(!$paperSize) {
            $paperSize = "A4";
        }
        $this->paperSize = $paperSize;
        parent::__construct("P", "mm", strtoupper($paperSize), $unicode, Config\Setting::getValue("Charset"), false);
        $this->SetCreator("WHMCS");
        $this->SetAuthor(Config\Setting::getValue("CompanyName"));
        $this->SetMargins(15, 25, 15);
        $this->SetFooterMargin(15);
        $this->SetAutoPageBreak(true, 25);
        $this->setLanguageArray(["a_meta_charset" => Config\Setting::getValue("Charset"), "a_meta_dir" => "ltr", "a_meta_language" => "en", "w_page" => "page"]);
    }
    public function setHeaderTplFile($headerTplFile)
    {
        $this->headerTplFile = $headerTplFile;
    }
    public function setFooterTplFile($footerTplFile)
    {
        $this->footerTplFile = $footerTplFile;
    }
    public function setTemplateVars(array $tplVars)
    {
        $this->templateVars = $tplVars;
    }
    public function Header()
    {
        if($this->headerTplFile) {
            foreach ($this->templateVars as $k => $v) {
                ${$k} = $v;
            }
            $pdf =& $this;
            include $this->headerTplFile;
        }
    }
    public function Footer()
    {
        if($this->footerTplFile) {
            foreach ($this->templateVars as $k => $v) {
                ${$k} = $v;
            }
            $pdf =& $this;
            include $this->footerTplFile;
        }
    }
    public function SetFont($family, $style = "", $size = NULL, $fontfile = "", $subset = "default", $out = true)
    {
        $adminFontSetting = Config\Setting::getValue("TCPDFFont");
        if(in_array($adminFontSetting, $this->fontlist)) {
            $familyOverride = $adminFontSetting;
        } elseif(in_array($family, $this->fontlist)) {
            $familyOverride = $family;
        } else {
            $familyOverride = PDF_FONT_NAME_MAIN;
        }
        parent::SetFont($familyOverride, $style, $size, $fontfile, $subset, $out);
    }
}

?>