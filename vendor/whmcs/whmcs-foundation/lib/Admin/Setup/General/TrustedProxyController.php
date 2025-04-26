<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Setup\General;

class TrustedProxyController
{
    private $cloudflareService;
    private $ipValidator;
    private $trustedProxyIpService;
    const SECURITY_TAB = 10;
    const TRUSTED_PROXY_ADDED_MESSAGE = "General Settings Changed. Trusted Proxy IP Added From CloudFlare®.";
    const PROXY_HEADER_SET_FORMAT = "General Settings Changed. Proxy Header Set to %s";
    const PROXY_HEADER_CHANGED_FORMAT = "General Settings Changed. Proxy Header Changed From %s to %s";
    public function __construct(Services\CloudflareService $cloudflareService = NULL, Services\IpValidator $ipValidator = NULL, Services\TrustedProxyIpService $trustedProxyIpService = NULL, Services\CloudflareHealthChecker $cloudflareChecker = NULL)
    {
        $this->cloudflareService = $cloudflareService ?? \DI::make("WHMCS\\Admin\\Setup\\General\\Services\\CloudflareService");
        $this->ipValidator = $ipValidator ?? \DI::make("WHMCS\\Admin\\Setup\\General\\Services\\IpValidator");
        $this->trustedProxyIpService = $trustedProxyIpService ?? \DI::make("WHMCS\\Admin\\Setup\\General\\Services\\TrustedProxyIpService");
        $this->cloudflareChecker = $cloudflareChecker ?? \DI::make("WHMCS\\Admin\\Setup\\General\\Services\\CloudflareHealthChecker");
    }
    public function addCloudflare(\WHMCS\Http\Message\ServerRequest $request)
    {
        if(defined("DEMO_MODE")) {
            $url = sprintf("%s/configgeneral.php?tab=%d", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl(), self::SECURITY_TAB);
            return new \WHMCS\Http\RedirectResponse($url);
        }
        $cloudflareIps = $this->cloudflareService->fetchIps();
        if($cloudflareIps->isEmpty()) {
            $url = sprintf("%s/configgeneral.php?tab=%d&error=trustedProxyCloudflareError", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl(), self::SECURITY_TAB);
            return new \WHMCS\Http\RedirectResponse($url);
        }
        $cloudflareIps = $cloudflareIps->map(function (string $ip) {
            return ["ip" => $ip, "note" => "CloudFlare"];
        });
        $invalidIp = $cloudflareIps->first(function (array $ip) {
            return $this->ipValidator->isInvalid($ip["ip"]);
        });
        if($invalidIp !== NULL) {
            $url = sprintf("%s/configgeneral.php?tab=%d&error=trustedProxyInvalidIp&invalidIp=%s", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl(), self::SECURITY_TAB, $invalidIp["ip"]);
            return new \WHMCS\Http\RedirectResponse($url);
        }
        if(!$this->cloudflareChecker->isCloudFlareIpProvided()) {
            $url = sprintf("%s/configgeneral.php?tab=%d&error=trustedProxyCloudflareIpNotDetected", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl(), self::SECURITY_TAB);
            return new \WHMCS\Http\RedirectResponse($url);
        }
        $this->trustedProxyIpService->addBatch($cloudflareIps);
        logAdminActivity(self::TRUSTED_PROXY_ADDED_MESSAGE);
        $this->updateProxyHeader();
        $url = sprintf("%s/configgeneral.php?tab=%d&success=true", \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl(), self::SECURITY_TAB);
        return new \WHMCS\Http\RedirectResponse($url);
    }
    private function updateProxyHeader() : void
    {
        $currentProxyHeader = \WHMCS\Config\Setting::getValue("proxyHeader");
        $proxyHeader = $this->cloudflareChecker->findCloudflareIpHeader();
        \WHMCS\Config\Setting::setValue("proxyHeader", $proxyHeader);
        $activity = empty($currentProxyHeader) ? sprintf(self::PROXY_HEADER_SET_FORMAT, $proxyHeader) : sprintf(self::PROXY_HEADER_CHANGED_FORMAT, $currentProxyHeader, $proxyHeader);
        logAdminActivity($activity);
    }
}

?>