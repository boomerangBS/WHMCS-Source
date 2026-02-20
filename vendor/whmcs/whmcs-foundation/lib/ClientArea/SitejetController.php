<?php


namespace WHMCS\ClientArea;
class SitejetController
{
    protected $sitejetAdapter;
    const PUBLISH_METADATA_SESSION_KEY = "SitejetPublishMetadata";
    protected function getService(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Service\Service
    {
        if(!is_null($request->query()->get("serviceId")) || !is_null($request->request()->get("serviceId"))) {
            throw new \WHMCS\Exception("Service ID must only be specified as a route path attribute");
        }
        $serviceId = $request->getAttribute("serviceId");
        return \WHMCS\Service\Service::find($serviceId);
    }
    protected function errorResponse() : \WHMCS\Http\Message\JsonResponse
    {
        return new \WHMCS\Http\Message\JsonResponse(["error" => \Lang::trans("clientareaerroroccured")]);
    }
    protected function getSitejetAdapter(\WHMCS\Service\Service $service) : \WHMCS\Service\Adapters\SitejetAdapter
    {
        if(!$this->sitejetAdapter) {
            $this->sitejetAdapter = \WHMCS\Service\Adapters\SitejetAdapter::factory($service);
        }
        return $this->sitejetAdapter;
    }
    protected function getPublishSessionData() : array
    {
        $sessionData = \WHMCS\Session::get(self::PUBLISH_METADATA_SESSION_KEY);
        if(is_string($sessionData)) {
            $sessionData = json_decode($sessionData, true) ?? [];
        } else {
            $sessionData = [];
        }
        return $sessionData;
    }
    protected function setPublishSessionData($data) : void
    {
        \WHMCS\Session::set(self::PUBLISH_METADATA_SESSION_KEY, json_encode($data));
    }
    public function publish(\WHMCS\Http\Message\ServerRequest $request)
    {
        $service = $this->getService($request);
        try {
            $publishMetadata = $this->getSitejetAdapter($service)->publishSitejet();
        } catch (\Throwable $e) {
            logActivity("Client area Sitejet publish failed: " . $e->getMessage());
            return $this->errorResponse();
        }
        $sessionData = $this->getPublishSessionData();
        $sessionData[$service->id] = $publishMetadata;
        $this->setPublishSessionData($sessionData);
        return new \WHMCS\Http\Message\JsonResponse(["success" => true, "progress_url" => fqdnRoutePath("clientarea-sitejet-publish-progress", $service->id)]);
    }
    public function getPublishProgress(\WHMCS\Http\Message\ServerRequest $request)
    {
        $service = $this->getService($request);
        $sessionData = $this->getPublishSessionData();
        $publishMetadata = $sessionData[$service->id] ?? NULL;
        if(is_null($publishMetadata)) {
            return $this->errorResponse();
        }
        try {
            $publishProgress = $this->getSitejetAdapter($service)->getSitejetPublishProgress($publishMetadata);
        } catch (\Throwable $e) {
            logActivity("Client area Sitejet publish failed: " . $e->getMessage());
            return $this->errorResponse();
        }
        return new \WHMCS\Http\Message\JsonResponse($publishProgress);
    }
    public function getSitePreviewImage(\WHMCS\Http\Message\ServerRequest $request)
    {
        $service = $this->getService($request);
        $sitejetAdapter = \WHMCS\Service\Adapters\SitejetAdapter::factory($service);
        try {
            $license = \App::getLicense();
            $imageUrl = $sitejetAdapter->getSitejetPreviewImageUrl($license, $request->get("refresh"));
            return new \WHMCS\Http\RedirectResponse($imageUrl);
        } catch (\Throwable $e) {
            $imageContent = file_get_contents(\DI::make("asset")->getFilesystemImgPath() . "/sitejet/preview_placeholder.png");
            return new \Laminas\Diactoros\Response((new \Laminas\Diactoros\StreamFactory())->createStream($imageContent), 200, ["Content-Type" => "image/png"]);
        }
    }
}

?>