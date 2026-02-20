<?php


namespace WHMCS\Utility;
class WebsiteScreenshotApiHandler
{
    protected $params = [];
    const DEFAULT_API_URL = "https://getsitescreenshot.com";
    const DEFAULT_WIDTH = 800;
    const DEFAULT_HEIGHT = 450;
    const CACHE_TTL_RETURN_CACHED = -1;
    const CACHE_TTL_FORCE_NEW = 604800;
    public function __construct(array $params)
    {
        $this->params = $params;
    }
    public static function createFromLicense(\WHMCS\License $license) : \self
    {
        $vendorServicesData = $license->getKeyData("VendorServices") ?: [];
        $params = $vendorServicesData["WebPros"]["WebsitePreview"]["screenshot_api"] ?? NULL;
        if(!is_array($params)) {
            throw new \WHMCS\Exception("License data contains no screenshot service parameters");
        }
        return new static($params);
    }
    private function getQueryString($params) : array
    {
        $params["sign"] = hash_hmac("sha256", implode("|", array_values($params)), base64_decode($this->params["signSecret"]));
        return http_build_query($params);
    }
    public function getWebsiteScreenshotUrl(string $websiteUrl, int $cacheTtlSec = self::CACHE_TTL_RETURN_CACHED, int $width = self::DEFAULT_WIDTH, int $height = self::DEFAULT_HEIGHT)
    {
        if(!filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
            throw new \WHMCS\Exception("Invalid URL provided");
        }
        $params = ["url" => $websiteUrl, "width" => $width, "height" => $height, "cache" => $cacheTtlSec];
        if($cacheTtlSec === self::CACHE_TTL_FORCE_NEW) {
            $params["nocache"] = \Illuminate\Support\Str::random(8);
        }
        $baseUrl = rtrim($this->params["base_url"] ?? self::DEFAULT_API_URL, "/");
        return sprintf("%s/?%s", $baseUrl, $this->getQueryString($params));
    }
}

?>