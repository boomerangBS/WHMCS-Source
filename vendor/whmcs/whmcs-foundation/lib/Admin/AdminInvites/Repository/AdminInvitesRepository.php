<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\AdminInvites\Repository;

class AdminInvitesRepository
{
    public function getAll() : \Illuminate\Database\Eloquent\Collection
    {
        return \WHMCS\Admin\AdminInvites\Model\AdminInvite::all();
    }
    public function getById($id) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        return \WHMCS\Admin\AdminInvites\Model\AdminInvite::findOrFail($id);
    }
    public function getByValidToken($token) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        return \WHMCS\Admin\AdminInvites\Model\AdminInvite::token($token)->notExpired()->firstOrFail();
    }
    public function create($email, int $expirationPeriodInDays, $invitedBy, string $template, string $language, string $firstName = NULL, string $lastname = NULL, string $username = 1, int $roleId = NULL, string $assignedDepartments = NULL, string $ticketNotifications = NULL, string $supportTicketSignature = NULL, string $privateNotes = false, $disable) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        $this->verifyCredentialsAreUnique($email, $username);
        $invite = new \WHMCS\Admin\AdminInvites\Model\AdminInvite();
        $invite->username = $username;
        $invite->expirationPeriodInDays = $expirationPeriodInDays;
        $invite->expiresAt = \WHMCS\Carbon::now()->addDays($expirationPeriodInDays);
        $invite->firstname = $firstName;
        $invite->lastname = $lastname;
        $invite->roleId = $roleId;
        $invite->email = $email;
        $invite->assignedDepartments = $assignedDepartments;
        $invite->ticketNotifications = $ticketNotifications;
        $invite->supportTicketSignature = $supportTicketSignature;
        $invite->privateNotes = $privateNotes;
        $invite->template = $template;
        $invite->language = $language;
        $invite->disable = $disable;
        $invite->token = $this->generateToken();
        $invite->sender()->associate($invitedBy);
        $invite->save();
        return $invite;
    }
    public function verifyCredentialsAreUnique($email = NULL, string $username) : void
    {
        if($this->recordExists("email", $email)) {
            throw new \WHMCS\Exception\Validation\DuplicateValue(\AdminLang::trans("quotes.emailexists"));
        }
        if($username && $this->recordExists("username", $username)) {
            throw new \WHMCS\Exception\Validation\DuplicateValue(\AdminLang::trans("administrators.userexists"));
        }
    }
    public function regenerateInvite(\WHMCS\Admin\AdminInvites\Model\AdminInvite $adminInvite) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        $adminInvite->token = $this->generateToken();
        $adminInvite->expiresAt = \WHMCS\Carbon::now()->addDays($adminInvite->expirationPeriodInDays);
        return $adminInvite->save();
    }
    public function delete(\WHMCS\Admin\AdminInvites\Model\AdminInvite $adminInvite) : \WHMCS\Admin\AdminInvites\Model\AdminInvite
    {
        return (bool) $adminInvite->delete();
    }
    public function recordActionPerformed($action) : void
    {
        try {
            $systemStats = \WHMCS\Config\Setting::getValue("SystemEventCache");
            $systemStats = json_decode($systemStats, true) ?? [];
            $systemStats["AdminInvites"][$action] = $systemStats["AdminInvites"][$action] ?? 0;
            $systemStats["AdminInvites"][$action] += 1;
            \WHMCS\Config\Setting::setValue("SystemEventCache", json_encode($systemStats));
        } catch (\WHMCS\Exception $exception) {
        }
    }
    private function recordExists($property, string $value)
    {
        $isAdminExists = \WHMCS\User\Admin::where($property, $value)->exists();
        $isAdminInviteExists = \WHMCS\Admin\AdminInvites\Model\AdminInvite::where($property, $value)->exists();
        return $isAdminExists || $isAdminInviteExists;
    }
    private function generateToken()
    {
        return hash("sha256", time() . \Illuminate\Support\Str::random());
    }
}

?>