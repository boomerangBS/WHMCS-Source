<?php


namespace WHMCS\Utility;
class MixPanel
{
    const MIXPANEL_PUBLIC_KEY_PRODUCTION = "4ac379f45de53a9b61429bbc9566db8d";
    const MIXPANEL_PUBLIC_KEY_DEVELOPMENT = "7ad15e0897e9e9a0f11aa6c9f0a7ff1c";
    const MIXPANEL_API_HOST = "https://api-eu.mixpanel.com";
    public static function getPublicKey()
    {
        $isDevLicense = \App::getLicense()->isDevLicense();
        return $isDevLicense ? self::MIXPANEL_PUBLIC_KEY_DEVELOPMENT : self::MIXPANEL_PUBLIC_KEY_PRODUCTION;
    }
    public static function getInitParams() : array
    {
        return ["ip" => false, "api_host" => self::MIXPANEL_API_HOST];
    }
    public static function isMixPanelTrackingEnabled()
    {
        return \WHMCS\Config\Setting::getValue("MixPanelTrackingEnabled") === "on";
    }
    public static function setMixPanelTrackingStatus($enabled)
    {
        $setting = \WHMCS\Config\Setting::setValue("MixPanelTrackingEnabled", $enabled ? "on" : "");
        $savedEnabledStatus = $setting->value === "on";
        return $savedEnabledStatus === $enabled;
    }
    public static function getMixpanelInitJs(\WHMCS\User\Admin $admin) : \WHMCS\User\Admin
    {
        $mixPanelPublicKey = self::getPublicKey();
        $mixPanelInitParams = json_encode(self::getInitParams());
        $adminIdentifier = md5($admin->uuid);
        return "<script>\n    let mixpanel_enabled;\n    (function () {\n        const cleanUrlPath = function (url) {\n            // replace numbers in the URL path \n            return url.replace(/\\b\\d+\\b/g, '<:param>');\n        };\n\n        const cleanUrlToTrack = function (urlString) {\n            const routingQueryParams = ['rp', 'action', 'a', 'view'];\n            const url = new URL(urlString);\n            url.pathname = cleanUrlPath(url.pathname);\n\n            // Drop all query params values except routing related params\n            url.searchParams.forEach(function (value, key, searchParams) {\n                if (!routingQueryParams.includes(key)) {\n                    searchParams.set(key, \"\");\n                } else {\n                    // cleanup sensitive info from the routing related param values\n                    searchParams.set(key, cleanUrlPath(value))\n                }\n            });\n\n            return url;\n        };\n\n        const urlToTrack = cleanUrlToTrack(window.location.href);\n        const documentReferrerToTrack = document.referrer ? cleanUrlToTrack(document.referrer) : \"\";\n\n        mixpanel.init('" . $mixPanelPublicKey . "', " . $mixPanelInitParams . ");\n\n        mixpanel.set_config({\n            \"property_blacklist\": ['\$current_url','\$initial_referrer', '\$referrer']\n        }); \n        mixpanel.identify('" . $adminIdentifier . "');\n        mixpanel.set_config({\n            \"property_blacklist\": ['\$initial_referrer']\n        }); \n\n        mixpanel.set_group('persona', ['admin']);\n        mixpanel.track_pageview({\n            \"page\": document.title,\n            \"current_url_search\": \"\",\n            \"\$current_url\": decodeURIComponent(urlToTrack.toString()),\n            \"current_url_path\": decodeURIComponent(urlToTrack.pathname),\n            \"\$referrer\": document.referrer\n                ? decodeURIComponent(cleanUrlToTrack(document.referrer).toString())\n                : \"\",\n        });\n        \n        mixpanel_enabled = true\n    })();\n</script>";
    }
}

?>