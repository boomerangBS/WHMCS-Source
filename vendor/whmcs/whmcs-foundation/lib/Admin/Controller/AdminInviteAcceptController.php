<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Controller;

class AdminInviteAcceptController
{
    private $adminInvitesService;
    private $auth;
    public function __construct(\WHMCS\Admin\AdminInvites\Services\AdminInvitesService $adminInvitesService = NULL, \WHMCS\Auth $auth = NULL)
    {
        $this->adminInvitesService = $adminInvitesService ?? \DI::make("WHMCS\\Admin\\AdminInvites\\Services\\AdminInvitesService");
        $this->auth = $auth ?? \DI::make("WHMCS\\Auth");
    }
    public function adminInviteAcceptForm(\WHMCS\Http\Message\ServerRequest $request)
    {
        $adminInvite = $this->getAdminInvite($request);
        $assetHelper = \DI::make("asset");
        $smarty = new \WHMCS\Smarty(true);
        $smarty->assign("BASE_PATH_IMG", $assetHelper->getImgPath());
        $smarty->assign("adminInvite", $adminInvite);
        $smarty->assign("csrfToken", generate_token("plain"));
        $smarty->assign("username", getLastInput("username"));
        $message = \WHMCS\FlashMessages::get();
        if($message && $message["type"] === "error") {
            $smarty->assign("errorMsg", $message["text"]);
        }
        return $smarty->fetch("admin-invite-sign-up.tpl");
    }
    public function adminInviteAcceptFormSubmit(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Http\RedirectResponse
    {
        $adminInvite = $this->getAdminInvite($request);
        $username = $request->request()->get("username");
        $password = $request->request()->get("password");
        $confirmPassword = $request->request()->get("confirmPassword");
        $password = trim(\WHMCS\Input\Sanitize::decode($password));
        $confirmPassword = trim(\WHMCS\Input\Sanitize::decode($confirmPassword));
        $failRoute = routePathWithQuery("admin-invite-prompt", [], ["auth_token" => $adminInvite->token]);
        try {
            $admin = $this->adminInvitesService->acceptInvite($adminInvite, $password, $confirmPassword, $username);
            $this->auth->getInfobyUsername($admin->username);
            $this->auth->setSessionVars();
            $this->auth->processLogin();
            return new \WHMCS\Http\RedirectResponse(routePath("admin-homepage"));
        } catch (\WHMCS\Exception\Validation\InvalidValue $e) {
        } catch (\WHMCS\Exception\Validation\InvalidLength $e) {
        } catch (\WHMCS\Exception\Validation\InvalidFirstCharacter $e) {
        } catch (\WHMCS\Exception\Validation\InvalidCharacters $e) {
        } catch (\WHMCS\Exception\Validation\DuplicateValue $e) {
            return (new \WHMCS\Http\RedirectResponse($failRoute))->withInput()->withError($e->getMessage());
        } catch (\Exception $e) {
            return (new \WHMCS\Http\RedirectResponse($failRoute))->withInput()->withError(\AdminLang::trans("global.reloadTryAgain"));
        }
    }
    private function getAdminInvite(\WHMCS\Http\Message\ServerRequest $request) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        $token = $request->get("auth_token");
        try {
            return $this->adminInvitesService->getByValidToken($token);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            throw new \WHMCS\Exception\HttpCodeException(\AdminLang::trans("errorPage.404.title"), \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND);
        }
    }
}

?>