<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment\Ioncube\Loader;

class Loader100100 implements \WHMCS\Environment\Ioncube\Contracts\LoaderInterface
{
    public static function getVersion()
    {
        return new \WHMCS\Version\SemanticVersion("10.1.0");
    }
    public function compatAssessment($phpVersion, \WHMCS\Environment\Ioncube\Contracts\InspectedFileInterface $file)
    {
        $fileEncodedWith = $file->getEncoderVersion();
        if(is_null($fileEncodedWith) || $fileEncodedWith === "") {
            throw new \Exception("Encoder version was not read");
        }
        $assessment = NULL;
        $defaultAssessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
        switch ($fileEncodedWith) {
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V13_PLUS_BUNDLED:
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V12_PLUS_BUNDLED:
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V10_PLUS_BUNDLED:
                $assessment = $this->compatV10Bundled($phpVersion, $file->getBundledPhpVersions());
                break;
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V13_PLUS_NON_BUNDLED:
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V12_PLUS_NON_BUNDLED:
                if(!$file->hasTargetPhpVersion()) {
                    $defaultAssessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_UNLIKELY;
                }
                break;
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V10_PLUS_NON_BUNDLED:
                if($file->canRunOnPhpVersion($phpVersion)) {
                    $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
                } else {
                    $assessment = $defaultAssessment;
                }
                break;
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V9_PLUS_NON_BUNDLED:
                $assessment = $this->compatV9PlusNonBundled($phpVersion, $file->getLoadedInPhp(), $file);
                break;
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_V8_OR_OLDER:
                if($file->getTargetPhpVersion()) {
                    if($file->canRunOnPhpVersion($phpVersion)) {
                        $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
                    } else {
                        $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                    }
                } elseif(version_compare($phpVersion, "7.0", "<")) {
                    $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_LIKELY;
                } else {
                    $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                }
                break;
            case \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ENCODER_VERSION_OUTDATED:
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                break;
            default:
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_UNLIKELY;
                if(is_null($assessment)) {
                    $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                }
                return $assessment;
        }
    }
    protected function compatV9PlusNonBundled($phpVersion, $verifiedLoadedInPhp, \WHMCS\Environment\Ioncube\Contracts\InspectedFileInterface $file = NULL)
    {
        $assessment = NULL;
        if(!$verifiedLoadedInPhp) {
            if($file->getTargetPhpVersion()) {
                if($file->canRunOnPhpVersion($phpVersion)) {
                    $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
                } else {
                    $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                }
            } elseif(version_compare($phpVersion, "7.0", "<=")) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_LIKELY;
            } elseif(version_compare($phpVersion, "8.0", ">=")) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif(in_array("5.6", $verifiedLoadedInPhp)) {
            if($phpVersion == "5.6" || $phpVersion == "7.0") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } elseif($phpVersion == "7.1" || $phpVersion == "7.2") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif(in_array("7.0", $verifiedLoadedInPhp)) {
            if($phpVersion == "7.0") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } elseif($phpVersion == "5.6") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_LIKELY;
            } else {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif(in_array("7.1", $verifiedLoadedInPhp)) {
            if($phpVersion == "5.6" || $phpVersion == "7.0") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            } elseif($phpVersion == "7.1" || $phpVersion == "7.2") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            }
        } elseif(in_array("7.2", $verifiedLoadedInPhp)) {
            if($phpVersion == "5.6" || $phpVersion == "7.0") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            } elseif($phpVersion == "7.1") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_LIKELY;
            } elseif($phpVersion == "7.2") {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            }
        }
        if(is_null($assessment)) {
            $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_UNLIKELY;
        }
        return $assessment;
    }
    protected function compatV10Bundled($phpVersion, array $bundledFor = [])
    {
        $assessment = NULL;
        if($phpVersion == "5.6") {
            if(in_array("5.6", $bundledFor) || in_array("7.0", $bundledFor)) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } else {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif($phpVersion == "7.0") {
            if(in_array("5.6", $bundledFor) || in_array("7.0", $bundledFor)) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } else {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif($phpVersion == "7.1") {
            if(in_array("7.1", $bundledFor) || in_array("7.2", $bundledFor)) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } else {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif($phpVersion == "7.2") {
            if(in_array("7.1", $bundledFor) || in_array("7.2", $bundledFor)) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } else {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif($phpVersion == "7.3") {
            if(in_array("7.1", $bundledFor) || in_array("7.2", $bundledFor) || in_array("7.3", $bundledFor)) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } else {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif($phpVersion === "7.4") {
            if(in_array("7.1", $bundledFor) || in_array("7.2", $bundledFor) || in_array("7.3", $bundledFor) || in_array("7.4", $bundledFor)) {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
            } else {
                $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
            }
        } elseif(version_compare($phpVersion, "8.1", ">=") && in_array($phpVersion, $bundledFor)) {
            $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
        }
        if(is_null($assessment)) {
            $assessment = \WHMCS\Environment\Ioncube\Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
        }
        return $assessment;
    }
    public function supportsBundledEncoding()
    {
        return true;
    }
}

?>