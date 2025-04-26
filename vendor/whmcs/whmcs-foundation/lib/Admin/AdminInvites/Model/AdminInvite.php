<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\AdminInvites\Model;

class AdminInvite extends \WHMCS\Model\AbstractModel
{
    protected $table = "tbladmin_invites";
    protected $columnMap = ["firstname" => "first_name", "lastname" => "last_name", "assignedDepartments" => "assigned_departments", "ticketNotifications" => "ticket_notify", "supportTicketSignature" => "support_ticket_signature", "privateNotes" => "private_notes", "expirationPeriodInDays" => "expiration_period_in_days", "expiresAt" => "expires_at", "invitedBy" => "invited_by"];
    protected $casts = ["disable" => "bool", "expires_at" => "datetime"];
    const ADMIN_INVITATION_EMAIL_TEMPLATE = "Admin Invitation";
    public function sender() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo("WHMCS\\User\\Admin", "invited_by", "id", "sender");
    }
    public function scopeToken(\Illuminate\Database\Eloquent\Builder $query, string $token) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("token", $token);
    }
    public function scopeNotExpired(\Illuminate\Database\Eloquent\Builder $query) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereDate("expires_at", ">", \WHMCS\Carbon::now());
    }
    public function isExpired()
    {
        return \WHMCS\Carbon::now()->gt($this->expiresAt);
    }
    public function daysLeftUntilInviteExpiration() : int
    {
        return \WHMCS\Carbon::now()->endOfDay()->diffInDays($this->expiresAt->clone()->endOfDay(), false);
    }
    public function setExpiresAtAttribute($value) : void
    {
        if($value instanceof \DateTimeInterface) {
            $this->attributes["expires_at"] = (new \WHMCS\Carbon($value))->endOfDay();
        } elseif(is_string($value)) {
            $this->attributes["expires_at"] = \WHMCS\Carbon::createFromFormat($this->getDateFormat(), $value)->endOfDay();
        }
    }
}

?>