<?php

namespace WHMCS\Table\Contracts;

interface TableInterface
{
    public function list(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\JsonResponse;
}

?>