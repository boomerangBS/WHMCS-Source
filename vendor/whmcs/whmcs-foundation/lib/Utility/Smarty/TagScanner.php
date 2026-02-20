<?php

namespace WHMCS\Utility\Smarty;

class TagScanner implements \WHMCS\Scheduling\Contract\JobInterface
{
    use \WHMCS\Scheduling\Jobs\JobTrait;
    const RESULT_CACHE_TTL_HOURS = 720;
    const TYPE_FILE = "file";
    const TYPE_EMAIL = "email";
    const DEPRECATED_SMARTY_BC_TAGS = ["{php}", "{include_php ", "{insert "];
    const DEPRECATED_SMARTY_BC_TAGS_CACHE_KEY = "SmartyBC";
    protected function getTemplateIterator($filePath) : \RecursiveIteratorIterator
    {
        $directory = new \RecursiveDirectoryIterator($filePath);
        $filter = new \RecursiveCallbackFilterIterator($directory, function ($current) {
            if($current->getFilename()[0] === ".") {
                return false;
            }
            if($current->isDir()) {
                return true;
            }
            $isTplFile = $current->getExtension() === "tpl";
            $isNotPleskApiFile = strpos($current->getPath(), "modules/servers/plesk/templates/api") === false;
            return $isTplFile && $isNotPleskApiFile;
        });
        return new \RecursiveIteratorIterator($filter);
    }
    protected function isAnyTagInLine($lineContent, array $tags) : array
    {
        foreach ($tags as $tag) {
            if(strpos($lineContent, $tag) !== false) {
                return true;
            }
        }
        return false;
    }
    protected function findFileLinesWithTag(\SplFileInfo $fileInfo, array $tags) : array
    {
        $results = [];
        $file = $fileInfo->openFile();
        for ($lineIndex = 1; !$file->eof(); $lineIndex++) {
            if($this->isAnyTagInLine($file->current(), $tags)) {
                $results[] = $lineIndex;
            }
            $file->next();
        }
        return $results;
    }
    protected function findContentLinesWithTag($content, array $tags) : array
    {
        $results = [];
        $lines = explode("\n", $content);
        foreach ($lines as $lineIndex => $line) {
            if($this->isAnyTagInLine($line, $tags)) {
                $results[] = $lineIndex + 1;
            }
        }
        return $results;
    }
    protected function getSmartyTrustedDirectories() : array
    {
        $securityPolicySetting = \DI::make("config")->smarty_security_policy;
        if(!is_array($securityPolicySetting)) {
            return [];
        }
        $trustedDirectories = [];
        foreach ($securityPolicySetting as $policySettings) {
            $trustedDirectories = array_merge($trustedDirectories, $policySettings["trusted_dir"] ?? []);
        }
        return array_unique($trustedDirectories);
    }
    protected function getDirectories() : array
    {
        $templateDirectories = [ROOTDIR . "/includes/hooks", ROOTDIR . "/modules", ROOTDIR . "/templates", ROOTDIR . "/admin/templates"];
        $trustedDirectories = $this->getSmartyTrustedDirectories();
        $isTemplateSubdirectory = function (string $dir) {
            static $templateDirectories = NULL;
            foreach ($templateDirectories as $templateDirectory) {
                if(strpos($dir, $templateDirectory) === 0) {
                    return true;
                }
            }
            return false;
        };
        foreach ($trustedDirectories as $trustedDirectory) {
            if(!$isTemplateSubdirectory($trustedDirectory)) {
                $templateDirectories[] = $trustedDirectory;
            }
        }
        return $templateDirectories;
    }
    protected function scanFileTemplates($tags) : array
    {
        $results = [];
        foreach ($this->getDirectories() as $dir) {
            if(!is_dir($dir)) {
            } else {
                $iterator = $this->getTemplateIterator($dir);
                foreach ($iterator as $fileInfo) {
                    $scanResults = $this->findFileLinesWithTag($fileInfo, $tags);
                    if(!empty($scanResults)) {
                        $results[] = ["type" => self::TYPE_FILE, "filePath" => $fileInfo->getRealPath(), "lineNumbers" => $scanResults];
                    }
                }
            }
        }
        usort($results, function ($result1, $result2) {
            return strcmp($result1["filePath"], $result2["filePath"]);
        });
        return $results;
    }
    protected function scanEmailTemplates($tags) : array
    {
        $results = [];
        \WHMCS\Mail\Template::chunk(10, function (\Illuminate\Support\Collection $templates) use($results) {
            static $tags = NULL;
            foreach ($templates as $template) {
                $lineNumbers = $this->findContentLinesWithTag($template->message, $tags);
                if(!empty($lineNumbers)) {
                    $results[] = ["type" => self::TYPE_EMAIL, "templateId" => $template->id, "templateName" => $template->name, "templateType" => $template->type, "templateLanguage" => $template->language, "lineNumbers" => $lineNumbers];
                }
            }
        });
        return $results;
    }
    protected function getFullCacheKey($cacheKeySuffix)
    {
        return "SmartyTagDiscovery." . $cacheKeySuffix;
    }
    public function findTagUsage($tags, string $cacheKeySuffix = false, $ignoreCache) : array
    {
        $cacheKey = $this->getFullCacheKey($cacheKeySuffix);
        $transientData = \WHMCS\TransientData::getInstance();
        if(!$ignoreCache) {
            $cachedResults = $transientData->retrieve($cacheKey);
            if(!is_null($cachedResults)) {
                return json_decode($cachedResults, true);
            }
        }
        $results = array_merge($this->scanFileTemplates($tags), $this->scanEmailTemplates($tags));
        $scanData = ["timestamp" => \WHMCS\Carbon::now()->toIso8601String(), "results" => $results];
        $transientData->store($cacheKey, json_encode($scanData), self::RESULT_CACHE_TTL_HOURS * 3600);
        return $scanData;
    }
    public function findTagUsageAndRequeue(...$args) : array
    {
        $scanData = $this->findTagUsage(...$args);
        if(0 < count($scanData["results"])) {
            \WHMCS\Scheduling\Jobs\Queue::add("smartybc.rescan", "WHMCS\\Utility\\Smarty\\TagScanner", "findTagUsageAndRequeue", $args, 1440, true);
        }
        return $scanData;
    }
    public function getScanResultCount($cacheKeySuffix) : int
    {
        $cacheKey = $this->getFullCacheKey($cacheKeySuffix);
        $transientData = \WHMCS\TransientData::getInstance();
        $cachedData = $transientData->retrieve($cacheKey);
        if(is_null($cachedData)) {
            return NULL;
        }
        $cachedResults = json_decode($cachedData, true) ?? [];
        if(!is_array($cachedResults["results"] ?? NULL)) {
            return NULL;
        }
        return count($cachedResults["results"]);
    }
}

?>