<?php

namespace WHMCS\Api\NG;

class ApiNgImplementationCollector
{
    public function getApiNgRouteProviders() : array
    {
        return ["WHMCS\\Api\\NG\\Versions\\V2\\ApiV2RouteProvider"];
    }
}

?>