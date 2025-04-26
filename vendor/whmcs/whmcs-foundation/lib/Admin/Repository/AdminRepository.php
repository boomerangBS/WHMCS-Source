<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Repository;

class AdminRepository
{
    public function createFromInvite(\WHMCS\Admin\AdminInvites\Model\AdminInvite $adminInvite, string $password = NULL, string $username) : \WHMCS\User\Admin
    {
        $username = $adminInvite->username ?: $username;
        if($this->recordExists("username", $username)) {
            throw new \WHMCS\Exception\Validation\DuplicateValue(\AdminLang::trans("administrators.userexists"));
        }
        if($this->recordExists("email", $adminInvite->email)) {
            throw new \WHMCS\Exception\Validation\DuplicateValue(\AdminLang::trans("administrators.duplicateEmail"));
        }
        $time = \WHMCS\Carbon::now()->toDateTimeString();
        $uuid = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $admin = new \WHMCS\User\Admin();
        $admin->uuid = $uuid;
        $admin->username = $username;
        $admin->firstname = $adminInvite->firstname;
        $admin->lastname = $adminInvite->lastname;
        $admin->roleId = $adminInvite->roleId;
        $admin->email = $adminInvite->email;
        $admin->supportdepts = $adminInvite->assignedDepartments ?? "";
        $admin->ticketnotifications = $adminInvite->ticketNotifications ?? "";
        $admin->signature = $adminInvite->supportTicketSignature ?? "";
        $admin->notes = $adminInvite->privateNotes ?? "";
        $admin->template = $adminInvite->template ?? "";
        $admin->language = $adminInvite->language ?? "";
        $admin->disabled = $adminInvite->disable ?? false;
        $admin->createdAt = $time;
        $admin->updatedAt = $time;
        $admin->save();
        $this->recordUnitCreated();
        $this->updatePassword($admin, $password);
        return $admin->refresh();
    }
    public function recordUnitCreated() : void
    {
        try {
            $systemStats = \WHMCS\Config\Setting::getValue("SystemEventCache");
            $systemStats = json_decode($systemStats, true) ?? [];
            isset($systemStats["AdminUser"]["created"]);
            isset($systemStats["AdminUser"]["created"]) ? $systemStats["AdminUser"]["created"] += 1 : $systemStats["AdminUser"]["created"];
            \WHMCS\Config\Setting::setValue("SystemEventCache", json_encode($systemStats));
        } catch (\Throwable $exception) {
        }
    }
    private function updatePassword(\WHMCS\User\Admin $admin, string $password) : void
    {
        $newAdmin = new \WHMCS\Auth();
        $newAdmin->getInfobyUsername($admin->username, false);
        $newAdmin->generateNewPasswordHashAndStore($password);
        $newAdmin->generateNewPasswordHashAndStoreForApi(md5($password));
    }
    private function recordExists($property, string $value)
    {
        return \WHMCS\User\Admin::where($property, $value)->exists();
    }
}

?>