<?php

namespace WHMCS\Updater\Version;

class Version8120beta1 extends IncrementalVersion
{
    protected $updateActions = ["createAdminInvitesTable", "createAdminAccessInvitationEmail", "addScheduledTicketActionPermissions", "ensureSitejetBuilderWelcomeEmailCreated"];
    public function ensureSitejetBuilderWelcomeEmailCreated() : void
    {
        $templateExists = \WHMCS\Mail\Template::where("name", "Sitejet Builder Welcome Email")->first();
        if(is_null($templateExists)) {
            $updater = new Version8100beta1(new \WHMCS\Version\SemanticVersion("8.10.0-beta.1"));
            $updater->createSitejetBuilderWelcomeEmail();
        }
    }
    public function createAdminInvitesTable() : \self
    {
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($schemaBuilder->hasTable("tbladmin_invites")) {
            return $this;
        }
        $schemaBuilder->create("tbladmin_invites", function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments("id");
            $table->string("username")->nullable()->default(NULL);
            $table->string("token", 100);
            $table->timestamp("created_at");
            $table->timestamp("updated_at");
            $table->timestamp("expires_at");
            $table->integer("expiration_period_in_days");
            $table->string("first_name");
            $table->string("last_name")->nullable()->default(NULL);
            $table->unsignedInteger("roleid")->default(1);
            $table->string("email");
            $table->text("assigned_departments")->nullable()->default(NULL);
            $table->text("ticket_notify")->nullable()->default(NULL);
            $table->text("support_ticket_signature")->nullable()->default(NULL);
            $table->text("private_notes")->nullable()->default(NULL);
            $table->text("template");
            $table->string("language", 32);
            $table->unsignedInteger("disable")->default(0);
            $table->unsignedInteger("invited_by")->default(0);
        });
        return $this;
    }
    public function createAdminAccessInvitationEmail() : \self
    {
        $templateExists = \WHMCS\Mail\Template::where("name", \WHMCS\Admin\AdminInvites\Model\AdminInvite::ADMIN_INVITATION_EMAIL_TEMPLATE)->first();
        if(!$templateExists) {
            $mailTemplate = new \WHMCS\Mail\Template();
            $mailTemplate->name = \WHMCS\Admin\AdminInvites\Model\AdminInvite::ADMIN_INVITATION_EMAIL_TEMPLATE;
            $mailTemplate->subject = "You've been invited to use WHMCS";
            $mailTemplate->language = "";
            $mailTemplate->plaintext = false;
            $mailTemplate->custom = false;
            $mailTemplate->type = "admin_invite";
            $mailTemplate->message = "<h2>You've been invited to use WHMCS.</h2>\n<p>{\$invite_sender_name} has invited you to start using WHMCS for {\$company_name}. WHMCS is a billing and automation platform for online businesses.</p>\n<p>To accept the invitation and set up your admin login credentials, click the link below:</p>\n<p><a href=\"{\$invite_accept_url}\">Accept Invitation</a></p>\n<p>This invitation will expire after {\$expiration_period_in_days} days. After this date, you will need to contact {\$company_name} to request a new invitation.</p>\n<p>{\$signature}</p>\n<p>Need help? See <a href=\"https://go.whmcs.com/2481/accept-admin-invite\">WHMCS's documentation</a> to get started.</p>";
            $mailTemplate->save();
        }
        return $this;
    }
    public function addScheduledTicketActionPermissions() : \self
    {
        $allPermissionIds = [\WHMCS\User\Admin\Permission::findId("View Scheduled Ticket Actions"), \WHMCS\User\Admin\Permission::findId("Create Scheduled Ticket Actions"), \WHMCS\User\Admin\Permission::findId("Edit Scheduled Ticket Actions"), \WHMCS\User\Admin\Permission::findId("Cancel Scheduled Ticket Actions")];
        $supportPermissionIds = array_diff($allPermissionIds, [\WHMCS\User\Admin\Permission::findId("Cancel Scheduled Ticket Actions")]);
        $fullAdminRoleId = \WHMCS\Database\Capsule::table("tbladminroles")->where("name", "Full Administrator")->value("id");
        $supportRoleId = \WHMCS\Database\Capsule::table("tbladminroles")->where("name", "Support Operator")->value("id");
        $existingRolesWithNewPermissionIds = \WHMCS\Database\Capsule::table("tbladminperms")->whereIn("permid", $allPermissionIds)->get(["permid", "roleid"])->groupBy("permid")->map(function (\Illuminate\Support\Collection $row) {
            return $row->pluck("roleid");
        })->toArray();
        $newValues = [];
        foreach ($allPermissionIds as $permissionId) {
            $existingRolesForPermission = $existingRolesWithNewPermissionIds[$permissionId] ?? NULL;
            if(is_array($existingRolesForPermission) && in_array($fullAdminRoleId, $existingRolesForPermission)) {
            } else {
                $newValues[] = ["roleid" => $fullAdminRoleId, "permid" => $permissionId];
            }
        }
        foreach ($supportPermissionIds as $permissionId) {
            $existingRolesForPermission = $existingRolesWithNewPermissionIds[$permissionId] ?? NULL;
            if(is_array($existingRolesForPermission) && in_array($supportRoleId, $existingRolesForPermission)) {
            } else {
                $newValues[] = ["roleid" => $supportRoleId, "permid" => $permissionId];
            }
        }
        if(!empty($newValues)) {
            \WHMCS\Database\Capsule::table("tbladminperms")->insert($newValues);
        }
        return $this;
    }
}

?>