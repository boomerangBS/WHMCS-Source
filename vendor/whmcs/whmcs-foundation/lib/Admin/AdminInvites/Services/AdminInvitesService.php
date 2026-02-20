<?php

namespace WHMCS\Admin\AdminInvites\Services;

class AdminInvitesService
{
    private $adminRepository;
    private $adminInvitesRepository;
    private $adminInviteNotificationService;
    private $validator;
    const DEFAULT_EXPIRATION_PERIOD_IN_DAYS = 7;
    const ADMIN_INVITE_ACCEPTED = "accepted";
    const ADMIN_INVITE_CANCELLED = "cancelled";
    const ADMIN_INVITE_RESENT = "resent";
    const ADMIN_INVITE_CREATED = "created";
    public function __construct(\WHMCS\Admin\Repository\AdminRepository $adminRepository, \WHMCS\Admin\AdminInvites\Repository\AdminInvitesRepository $adminInvitesRepository, AdminInviteNotificationService $adminInviteNotificationService, \WHMCS\Validate $validator)
    {
        $this->adminRepository = $adminRepository;
        $this->adminInvitesRepository = $adminInvitesRepository;
        $this->adminInviteNotificationService = $adminInviteNotificationService;
        $this->validator = $validator;
    }
    public function inviteNewAdmin($email, $invitedBy, string $template, string $language, string $firstName = NULL, string $lastname = NULL, string $username = 1, int $roleId = "", string $assignedDepartments = "", string $ticketNotifications = "", string $supportTicketSignature = "", string $privateNotes = false, $disable) : void
    {
        $adminInvite = $this->adminInvitesRepository->create($email, self::DEFAULT_EXPIRATION_PERIOD_IN_DAYS, $invitedBy, $template, $language, $firstName, $lastname, $username, $roleId, $assignedDepartments, $ticketNotifications, $supportTicketSignature, $privateNotes, $disable);
        $this->adminInvitesRepository->recordActionPerformed(self::ADMIN_INVITE_CREATED);
        try {
            logActivity(sprintf("Admin \"%s\" sent an invite to \"%s\" to create a new admin%s.", $invitedBy->username, $adminInvite->email, $this->prepareUsernameLogString($adminInvite)));
        } catch (\Throwable $e) {
        }
        $this->adminInviteNotificationService->sendNotification($adminInvite);
    }
    public function getAll() : \Illuminate\Database\Eloquent\Collection
    {
        return $this->adminInvitesRepository->getAll();
    }
    public function resend($inviteId, $admin) : void
    {
        $adminInvite = $this->adminInvitesRepository->getById($inviteId);
        $this->adminInvitesRepository->regenerateInvite($adminInvite);
        $this->adminInvitesRepository->recordActionPerformed(self::ADMIN_INVITE_RESENT);
        try {
            logActivity(sprintf("Admin \"%s\" resent an invite to \"%s\" to create a new admin%s.", $admin->username, $adminInvite->email, $this->prepareUsernameLogString($adminInvite)));
        } catch (\Throwable $e) {
        }
        $this->adminInviteNotificationService->sendNotification($adminInvite);
    }
    public function cancel($inviteId, $admin) : int
    {
        $adminInvite = $this->adminInvitesRepository->getById($inviteId);
        $this->adminInvitesRepository->recordActionPerformed(self::ADMIN_INVITE_CANCELLED);
        try {
            logActivity("Admin \"" . $admin->username . "\" cancelled the invite for \"" . $adminInvite->email . "\".");
        } catch (\Throwable $e) {
        }
        return $this->adminInvitesRepository->delete($adminInvite);
    }
    public function getByValidToken($token) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        return $this->adminInvitesRepository->getByValidToken($token);
    }
    public function acceptInvite(\WHMCS\Admin\AdminInvites\Model\AdminInvite $adminInvite, string $password, string $confirmPassword = NULL, string $username) : \WHMCS\User\Admin
    {
        if(!$username && !$adminInvite->username) {
            throw new \WHMCS\Exception\Validation\InvalidValue(\AdminLang::trans("administrators.usererror"));
        }
        if($username) {
            (new \WHMCS\User\Admin())->validateUsername($username);
        }
        if(strlen($password) === 0) {
            throw new \WHMCS\Exception\Validation\InvalidValue(\AdminLang::trans("administrators.pwerror"));
        }
        if(strlen($confirmPassword) === 0) {
            throw new \WHMCS\Exception\Validation\InvalidValue(\AdminLang::trans("administrators.pwconfirmerror"));
        }
        if(!$this->validator->validate("pwstrength", "password", "", "", $password)) {
            throw new \WHMCS\Exception\Validation\InvalidValue(\AdminLang::trans("administrators.pwstrengthfail"));
        }
        if($password !== $confirmPassword) {
            throw new \WHMCS\Exception\Validation\InvalidValue(\AdminLang::trans("administrators.pwmatcherror"));
        }
        $admin = $this->adminRepository->createFromInvite($adminInvite, $password, $username);
        $this->adminInvitesRepository->recordActionPerformed(self::ADMIN_INVITE_ACCEPTED);
        try {
            $newAdminUsername = \WHMCS\User\Admin::where("email", $adminInvite->email)->first()->getAttribute("username");
            logActivity("Admin \"" . $newAdminUsername . "\" successfully accepted the new admin invitation from \"" . $adminInvite->sender->username . "\".");
        } catch (\Throwable $e) {
        }
        $this->adminInvitesRepository->delete($adminInvite);
        return $admin;
    }
    private function prepareUsernameLogString(\WHMCS\Admin\AdminInvites\Model\AdminInvite $adminInvite) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        return !empty($adminInvite->username) ? sprintf(" \"%s\"", $adminInvite->username) : "";
    }
}

?>