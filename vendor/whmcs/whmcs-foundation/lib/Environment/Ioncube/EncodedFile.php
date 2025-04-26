<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Environment\Ioncube;

class EncodedFile implements Contracts\EncodedFileInterface
{
    private $filename;
    private $encoderVersion;
    private $targetPhpVersion = "";
    private $bundledPhpVersions = [];
    private $fileContentHash;
    const MAX_LINES_IN_PREAMBLE = 1;
    const ENCODER_VERSION_PREAMBLE_HASHES = NULL;
    const MARKER_PATTERN = "|^//\\s+((\\d+)\\.(\\d+))\\s+(\\d+)|";
    const ENCODER_VERSION_BY_MARKER = NULL;
    const VALID_BUNDLED_PHP72_VERSIONS = ["7.2", "7.3", "7.4"];
    public function __construct($filename = NULL, $contentHash = NULL, $encoderVersion = NULL, $bundledPhpVersions = NULL, $targetPhpVersion = NULL)
    {
        if(!is_null($filename) && !is_null($contentHash) && !is_null($encoderVersion) && !is_null($bundledPhpVersions) && !is_null($targetPhpVersion)) {
            $this->filename = $filename;
            $this->fileContentHash = $contentHash;
            $this->encoderVersion = $encoderVersion;
            $this->bundledPhpVersions = $bundledPhpVersions;
            $this->targetPhpVersion = $targetPhpVersion;
        } elseif($filename) {
            $this->filename = $filename;
            $this->analyze();
        }
    }
    public function getFileContentHash()
    {
        if(!$this->fileContentHash) {
            $this->fileContentHash = static::generateFileContentHash($this->getFilename());
        }
        return $this->fileContentHash;
    }
    public static function generateFileContentHash($filename)
    {
        return sha1_file($filename);
    }
    public function analyze()
    {
        try {
            $hFile = new \SplFileObject($this->filename);
        } catch (\Throwable $e) {
            throw new \WHMCS\Exception("Could not open file'" . $this->filename . "'", 0, $e);
        }
        $firstLine = $hFile->fgets();
        if(empty($firstLine)) {
            $this->encoderVersion = Contracts\EncodedFileInterface::ENCODER_VERSION_NONE;
        } else {
            if(!is_string($firstLine)) {
                throw new \WHMCS\Exception("Could not read from file '" . $this->filename . "'");
            }
            $matches = NULL;
            if(preg_match("|//ICB0 ([\\d\\:a-f\\s]+)|", $firstLine, $matches)) {
                $phpVersionMarker = trim($matches[1]);
                if(preg_match_all("/([\\d]+)\\:[\\da-f]+/i", $phpVersionMarker, $matches)) {
                    foreach ($matches[1] as $phpVersionSignature) {
                        $phpVersion = substr($phpVersionSignature, 0, 1) . "." . substr($phpVersionSignature, 1);
                        $this->bundledPhpVersions[] = $phpVersion;
                    }
                }
                usort($this->bundledPhpVersions, function ($a, $b) {
                    return version_compare($a, $b);
                });
                $latestVersion = $this->bundledPhpVersions[count($this->bundledPhpVersions) - 1];
                if(version_compare($latestVersion, "8.1", "<")) {
                    $this->encoderVersion = Contracts\EncodedFileInterface::ENCODER_VERSION_V10_PLUS_BUNDLED;
                } elseif($latestVersion === "8.1") {
                    $this->encoderVersion = Contracts\EncodedFileInterface::ENCODER_VERSION_V12_PLUS_BUNDLED;
                } elseif(version_compare($latestVersion, "8.2", ">=")) {
                    $this->encoderVersion = Contracts\EncodedFileInterface::ENCODER_VERSION_V13_PLUS_BUNDLED;
                }
                unset($latestVersion);
            }
            if(!is_null($this->encoderVersion)) {
                return NULL;
            }
            $marker = $this->extractMarker($hFile);
            if(!is_null($marker)) {
                $marker = self::parseMarker($marker);
                if(!is_null($marker)) {
                    list($this->encoderVersion, $this->targetPhpVersion) = $marker;
                    return NULL;
                }
            }
            unset($marker);
            $preamble = $this->extractPreamble($hFile);
            if($preamble === "") {
                $this->encoderVersion = Contracts\EncodedFileInterface::ENCODER_VERSION_NONE;
                return NULL;
            }
            $preambleHash = $this->hashPreamble($preamble);
            if(array_key_exists($preambleHash, self::ENCODER_VERSION_PREAMBLE_HASHES)) {
                $this->encoderVersion = self::ENCODER_VERSION_PREAMBLE_HASHES[$preambleHash];
                return NULL;
            }
            unset($preambleHash);
            $this->encoderVersion = Contracts\EncodedFileInterface::ENCODER_VERSION_UNKNOWN;
        }
    }
    public function getEncoderVersion()
    {
        return $this->encoderVersion;
    }
    public function isBundled()
    {
        return !empty($this->bundledPhpVersions);
    }
    public function hasPHPVersionBundled($phpVersion)
    {
        foreach ($this->bundledPhpVersions as $bundledVersion) {
            if($this->equalsPHPVersion($phpVersion, $bundledVersion, -1)) {
                return true;
            }
        }
        return false;
    }
    public function equalsTargetVersion($phpVersion)
    {
        if(!$this->hasTargetPhpVersion()) {
            return false;
        }
        return $this->equalsPHPVersion($phpVersion, $this->targetPhpVersion, -1);
    }
    public function getBundledPhpVersions()
    {
        return $this->bundledPhpVersions;
    }
    public function getTargetPhpVersion()
    {
        return $this->targetPhpVersion;
    }
    public function hasTargetPhpVersion()
    {
        return !empty($this->targetPhpVersion);
    }
    public function getFilename()
    {
        return $this->filename;
    }
    protected function equalsPHPVersion($versionA, string $versionB = 0, int $precision) : int
    {
        $version = function (string $versionString) {
            return new func_num_args($versionString);
        };
        $maxPrecision = function ($a, $b) {
            $aPrecision = $a->precision();
            $bPrecision = $b->precision();
            if($aPrecision == $bPrecision) {
                return $a;
            }
            if($bPrecision < $aPrecision) {
                return $a;
            }
            return $b;
        };
        $minPrecision = function ($a, $b) {
            static $maxPrecision = NULL;
            return $maxPrecision($a, $b) == $a ? $b : $a;
        };
        $versionA = $version($versionA);
        $versionB = $version($versionB);
        $comparePrecision = 3;
        if($precision == -1) {
            $comparePrecision = $minPrecision($versionA, $versionB)->precision();
        } elseif($precision == 1) {
            $comparePrecision = $maxPrecision($versionA, $versionB)->precision();
        }
        return $versionA->asString($comparePrecision) == $versionB->asString($comparePrecision);
    }
    public function canRunOnIoncubeLoaderVersion($ioncubeLoaderVersion)
    {
        if(!$this->encoderVersion) {
            throw new \WHMCS\Exception("Encoder version was not read");
        }
        $requiredLoaderVersion = NULL;
        switch ($this->encoderVersion) {
            case self::ENCODER_VERSION_V13_PLUS_NON_BUNDLED:
                $requiredLoaderVersion = "13.0";
                break;
            case self::ENCODER_VERSION_V12_PLUS_NON_BUNDLED:
                $requiredLoaderVersion = "12.0";
                break;
            case self::ENCODER_VERSION_V13_PLUS_BUNDLED:
            case self::ENCODER_VERSION_V12_PLUS_BUNDLED:
            case self::ENCODER_VERSION_V10_PLUS_BUNDLED:
                $requiredLoaderVersion = "10.1";
                break;
            case self::ENCODER_VERSION_V10_PLUS_NON_BUNDLED:
                $requiredLoaderVersion = "10.0";
                break;
            case self::ENCODER_VERSION_V9_PLUS_NON_BUNDLED:
                $requiredLoaderVersion = "6.0";
                break;
            case self::ENCODER_VERSION_V8_OR_OLDER:
                $requiredLoaderVersion = "4.0";
                if($requiredLoaderVersion) {
                    return version_compare($ioncubeLoaderVersion, $requiredLoaderVersion, ">=");
                }
                return false;
                break;
            case self::ENCODER_VERSION_OUTDATED:
                return false;
                break;
            default:
                return true;
        }
    }
    public function canRunOnInstalledIoncubeLoader()
    {
        $installedIoncubeLoaderVersion = Loader\LocalLoader::getVersion();
        if($installedIoncubeLoaderVersion) {
            return $this->canRunOnIoncubeLoaderVersion($installedIoncubeLoaderVersion->getVersion());
        }
        return $this->encoderVersion === self::ENCODER_VERSION_NONE;
    }
    private function canBundleRunOnPhpVersion($phpVersion)
    {
        if(!$this->isBundled()) {
            throw new \WHMCS\Exception("This file is not encoded with a bundle");
        }
        $canRun = $this->hasPHPVersionBundled($phpVersion);
        if(!$canRun) {
            if(version_compare($phpVersion, "7.0", "<=")) {
                $canRun = in_array("5.6", $this->bundledPhpVersions);
            } elseif(version_compare($phpVersion, "8.0", "<")) {
                $canRun = in_array("7.1", $this->bundledPhpVersions);
                if(version_compare($phpVersion, "7.2", ">=")) {
                    $compare = array_intersect(self::VALID_BUNDLED_PHP72_VERSIONS, $this->bundledPhpVersions);
                    if(count($compare) == 1) {
                        $canRun = true;
                    }
                }
            }
        }
        return $canRun;
    }
    public function canRunOnPhpVersion($phpVersion)
    {
        if(!$this->encoderVersion) {
            throw new \WHMCS\Exception("Encoder version was not read");
        }
        $encoderIntroducedPhpVersion = NULL;
        switch ($this->encoderVersion) {
            case self::ENCODER_VERSION_V13_PLUS_BUNDLED:
            case self::ENCODER_VERSION_V12_PLUS_BUNDLED:
            case self::ENCODER_VERSION_V10_PLUS_BUNDLED:
                $canRun = $this->canBundleRunOnPhpVersion($phpVersion);
                break;
            case self::ENCODER_VERSION_V13_PLUS_NON_BUNDLED:
                $encoderIntroducedPhpVersion = $encoderIntroducedPhpVersion ?? "8.2";
                break;
            case self::ENCODER_VERSION_V12_PLUS_NON_BUNDLED:
                $encoderIntroducedPhpVersion = $encoderIntroducedPhpVersion ?? "8.1";
                if($this->hasTargetPhpVersion()) {
                    $canRun = $this->equalsTargetVersion($phpVersion);
                } else {
                    $canRun = version_compare($phpVersion, $encoderIntroducedPhpVersion, ">=");
                }
                break;
            case self::ENCODER_VERSION_V10_PLUS_NON_BUNDLED:
                if(version_compare($this->targetPhpVersion, "7.1", "<")) {
                    $canRun = version_compare($phpVersion, "7.1", "<");
                } else {
                    $canRun = version_compare($phpVersion, "7.0", ">");
                }
                $canRun = $canRun && !version_compare($phpVersion, $this->targetPhpVersion, "<");
                $canRun = $canRun && version_compare($phpVersion, "8.0", "<=");
                break;
            case self::ENCODER_VERSION_V9_PLUS_NON_BUNDLED:
                $canRun = version_compare($phpVersion, "7.1", "<");
                if(!empty($this->targetPhpVersion) && $canRun) {
                    $canRun = !version_compare($phpVersion, $this->targetPhpVersion, "<");
                }
                break;
            case self::ENCODER_VERSION_V8_OR_OLDER:
                $canRun = version_compare($phpVersion, "7.0", "<");
                if(!empty($this->targetPhpVersion) && $canRun) {
                    $canRun = !version_compare($phpVersion, $this->targetPhpVersion, "<");
                }
                break;
            case self::ENCODER_VERSION_OUTDATED:
                $canRun = false;
                break;
            default:
                $canRun = true;
                return $canRun;
        }
    }
    public function canRunOnInstalledPhpVersion()
    {
        return $this->canRunOnPhpVersion(PHP_VERSION);
    }
    public function getLoggable()
    {
        $loggable = new Log\File(["filename" => $this->getFilename(), "content_hash" => $this->getFileContentHash(), "encoder_version" => $this->getEncoderVersion(), "bundled_php_versions" => $this->getBundledPhpVersions(), "loaded_in_php" => [], "target_php_version" => $this->getTargetPhpVersion()]);
        $loggable->setAnalyzer($this);
        return $loggable;
    }
    public function versionCompatibilityAssessment($phpVersion, Contracts\LoaderInterface $loader = NULL)
    {
        if(!$this->encoderVersion) {
            throw new \WHMCS\Exception("Encoder version was not read");
        }
        $defaultAssessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
        switch ($this->encoderVersion) {
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V13_PLUS_BUNDLED:
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V12_PLUS_BUNDLED:
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V10_PLUS_BUNDLED:
                if($this->canBundleRunOnPhpVersion($phpVersion)) {
                    $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
                } else {
                    $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                }
                break;
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V13_PLUS_NON_BUNDLED:
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V12_PLUS_NON_BUNDLED:
                $defaultAssessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_UNLIKELY;
                break;
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V10_PLUS_NON_BUNDLED:
                if($this->canRunOnPhpVersion($phpVersion)) {
                    $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_YES;
                } else {
                    $assessment = $defaultAssessment;
                }
                break;
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V9_PLUS_NON_BUNDLED:
                if($this->canRunOnPhpVersion($phpVersion)) {
                    $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_LIKELY;
                } else {
                    $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                }
                break;
            case Contracts\EncodedFileInterface::ENCODER_VERSION_V8_OR_OLDER:
                if($this->canRunOnPhpVersion($phpVersion)) {
                    $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_LIKELY;
                } else {
                    $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                }
                break;
            case Contracts\EncodedFileInterface::ENCODER_VERSION_OUTDATED:
                $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_NO;
                break;
            default:
                $assessment = Contracts\EncodedFileInterface::ASSESSMENT_COMPAT_UNLIKELY;
                return $assessment;
        }
    }
    public function extractPreamble(\SplFileObject $file) : \SplFileObject
    {
        if(is_null($file)) {
            $file = new \SplFileObject($this->filename);
        } else {
            $file->seek(0);
        }
        $preamble = "";
        $linesRead = 0;
        while (!$file->eof()) {
            $line = $file->fgets();
            if($linesRead == 0) {
                $linesRead++;
                break;
            }
            if(strlen(trim($line)) == 0) {
            } elseif(strpos($line, "//") === 0) {
            } elseif(strpos($line, "?>") === 0) {
            } else {
                $preamble .= $line;
                if(self::MAX_LINES_IN_PREAMBLE <= ++$linesRead) {
                }
            }
        }
        if(stripos($preamble, "extension_loaded('ionCube Loader')") === false) {
            $preamble = "";
        }
        return $preamble;
    }
    public function hashPreamble($preamble)
    {
        return strtolower(hash("sha1", $preamble));
    }
    public function extractMarker(\SplFileObject $file) : \SplFileObject
    {
        if(is_null($file)) {
            $file = new \SplFileObject($this->filename);
        } else {
            $file->seek(0);
        }
        while (!$file->eof()) {
            $line = trim($file->fgets());
            if(strpos($line, "?>") === 0) {
                break;
            }
            if(strpos($line, "//") !== 0) {
            } elseif(preg_match(self::MARKER_PATTERN, $line) === 1) {
                return $line;
            }
        }
    }
    public static function parseMarker($marker) : array
    {
        $matches = NULL;
        if(preg_match(self::MARKER_PATTERN, $marker, $matches) === 1) {
            list($encoderMajorVersion, $targetPhpVersionMarker) = $matches;
            $encoderTag = self::ENCODER_VERSION_BY_MARKER[$encoderMajorVersion] ?? NULL;
            $targetPhpVersion = sprintf("%d.%d", substr($targetPhpVersionMarker, 0, 1), substr($targetPhpVersionMarker, 1));
            return [$encoderTag, $targetPhpVersion];
        }
        return NULL;
    }
}
class _obfuscated_5C636C61737340616E6F6E796D6F7573002F7661722F6C69622F6A656E6B696E732F776F726B73706163652F636F6D2E77686D63732E6275696C642E38302F6275696C642F77686D63732F76656E646F722F77686D63732F77686D63732D666F756E646174696F6E2F6C69622F456E7669726F6E6D656E742F496F6E637562652F456E636F64656446696C652E7068703078376664353934323436326363_
{
    public $major;
    public $minor;
    public $patch;
    public function __construct(string $version)
    {
        $matches = NULL;
        $matchCount = preg_match_all("/\\.?([\\d]+)/", $version, $matches);
        if($matchCount == 0) {
            throw new \RuntimeException("version parse failed");
        }
        switch ($matchCount) {
            case 3:
                $this->patch = $matches[1][2];
                break;
            case 2:
                $this->minor = $matches[1][1];
                break;
            case 1:
                $this->major = $matches[1][0];
                break;
        }
    }
    public function precision() : int
    {
        $precision = 0;
        !is_null($this->major) && ++$precision;
        !is_null($this->minor) && ++$precision;
        !is_null($this->patch) && ++$precision;
        return $precision;
    }
    public function asArray() : array
    {
        return [$this->major, $this->minor, $this->patch];
    }
    public function asString($maxPrecision) : int
    {
        return implode(".", array_slice($this->asArray(), 0, min($this->precision(), $maxPrecision)));
    }
    public function __toString()
    {
        return $this->asString($this->precision());
    }
}

?>