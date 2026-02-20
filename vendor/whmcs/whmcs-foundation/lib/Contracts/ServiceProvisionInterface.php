<?php

namespace WHMCS\Contracts;

interface ServiceProvisionInterface
{
    public function provision($model, array $params);
    public function configure($model, array $params);
    public function cancel($model, array $params);
    public function renew($model, array $response);
    public function install(\WHMCS\ServiceInterface $model, array $params);
}

?>