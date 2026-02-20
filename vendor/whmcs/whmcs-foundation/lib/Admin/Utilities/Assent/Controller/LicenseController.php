<?php

namespace WHMCS\Admin\Utilities\Assent\Controller;

class LicenseController
{
    const REQUEST_ATTRIBUTE_NO_WRITE_CONFIG = "no_write_license_key_update";
    public function licensedRequired(\WHMCS\Http\Message\ServerRequest $request)
    {
        $view = new \WHMCS\Admin\Utilities\Assent\View\AssentPage("activate-license");
        $view->setTitle("Activate License");
        $view->setAdminUser($request->getAttribute("authenticatedUser"));
        return $view;
    }
    public function updateLicenseKey(\WHMCS\Http\Message\ServerRequest $request)
    {
        $licenseKey = $request->request()->get("license_key");
        $license = \DI::make("license");
        if(!$license->isValidLicenseKey($licenseKey)) {
            return new \WHMCS\Http\Message\JsonResponse(["errorMessage" => \AdminLang::trans("license.invalidkey"), "success" => false]);
        }
        $license->setLicenseKey($licenseKey);
        if($request->getAttribute(static::REQUEST_ATTRIBUTE_NO_WRITE_CONFIG)) {
            return new \WHMCS\Http\Message\JsonResponse(["redirect" => routePath("admin-homepage"), "success" => true]);
        }
        try {
            $this->factoryConfigurationWriter()->setValue("license", $licenseKey);
        } catch (\Exception $e) {
            return new \WHMCS\Http\Message\JsonResponse(["errorMessage" => $e->getMessage(), "success" => false]);
        }
        return new \WHMCS\Http\Message\JsonResponse(["redirect" => routePath("admin-homepage"), "success" => true]);
    }
    protected function factoryConfigurationWriter()
    {
        return new \WHMCS\Config\ApplicationWriter();
    }
}

?>