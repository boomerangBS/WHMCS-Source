<?php

namespace WHMCS\Admin\Setup;

interface Oauth2MailControllerInterface
{
    public function getStoredClientSecret(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\Message\ServerRequest;
}

?>