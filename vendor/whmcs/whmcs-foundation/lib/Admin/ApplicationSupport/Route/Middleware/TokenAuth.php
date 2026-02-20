<?php

namespace WHMCS\Admin\ApplicationSupport\Route\Middleware;

class TokenAuth implements \WHMCS\Route\Contracts\Middleware\StrategyInterface
{
    use \WHMCS\Route\Middleware\Strategy\DelegatingMiddlewareTrait;
    public function _process(\WHMCS\Http\Message\ServerRequest $request, \Interop\Http\ServerMiddleware\DelegateInterface $delegate)
    {
        if(\WHMCS\Auth::isLoggedIn()) {
            return new \WHMCS\Http\RedirectResponse(\WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl());
        }
        $token = $request->get("auth_token");
        if(!$token) {
            throw new \WHMCS\Exception\HttpCodeException(\AdminLang::trans("errorPage.404.title"), \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
        $adminInvitesService = \DI::make("WHMCS\\Admin\\AdminInvites\\Services\\AdminInvitesService");
        try {
            $adminInvitesService->getByValidToken($token);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            throw new \WHMCS\Exception\HttpCodeException(\AdminLang::trans("errorPage.404.title"), \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
        return $request;
    }
}

?>