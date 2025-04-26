<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket\ScheduledActions;

class TicketScheduledAction extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblticketactions";
    protected $columnMap = ["createdById" => "created_by_admin_id", "editedById" => "updated_by_admin_id", "skipFlags" => "skip_flags", "ticketId" => "ticket_id", "statusAt" => "status_at", "processorId" => "processor_id"];
    protected $dates = ["scheduled", "statusAt"];
    const STATUS_SCHEDULED = "scheduled";
    const STATUS_IN_PROGRESS = "in_progress";
    const STATUS_COMPLETED = "completed";
    const STATUS_FAILED = "failed";
    const STATUS_CANCELLED = "cancelled";
    const STATUS_SKIPPED = "skipped";
    const SCHEDULED_ACTION_STATUSES = NULL;
    public function ticket() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo("WHMCS\\Support\\Ticket", "ticket_id", "id", "ticket");
    }
    public function createdAdmin() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->HasOne("WHMCS\\User\\Admin", "id", "created_by_admin_id");
    }
    public function editedAdmin() : \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->HasOne("WHMCS\\User\\Admin", "id", "updated_by_admin_id");
    }
    public function skip() : \self
    {
        $this->updateStatus(static::STATUS_SKIPPED);
        return $this;
    }
    public function clearSkipFlags() : \self
    {
        $this->skipFlags = new SkipFlagMask(0);
        return $this;
    }
    public function scheduled() : \WHMCS\Carbon
    {
        return $this->scheduled;
    }
    public function schedule(\WHMCS\Carbon $date) : \self
    {
        $this->scheduled = $date;
        $this->updateStatus(static::STATUS_SCHEDULED);
        return $this;
    }
    public function cancel() : \self
    {
        $this->updateStatus(static::STATUS_CANCELLED);
        return $this;
    }
    public function canCancel()
    {
        return $this->isStatus(self::STATUS_SCHEDULED) || $this->isStatus(self::STATUS_FAILED);
    }
    public function fail() : \self
    {
        $this->updateStatus(static::STATUS_FAILED);
        return $this;
    }
    public function complete() : \self
    {
        $this->updateStatus(static::STATUS_COMPLETED);
        return $this;
    }
    public function inProgress() : \self
    {
        $this->updateStatus(static::STATUS_IN_PROGRESS);
        return $this;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function isStatus($status)
    {
        return $this->status === $status;
    }
    public function getAction() : \WHMCS\Support\Ticket\Actions\AbstractAction
    {
        $action = $this->getActionInstance();
        if(is_int($this->createdById)) {
            $action->attributeToAdmin($this->createdById);
        }
        return $action->init($this->ticket, $action->unserializeParameters($this->parameters ?? ""));
    }
    public function getActionInstance() : \WHMCS\Support\Ticket\Actions\AbstractAction
    {
        return \WHMCS\Support\Ticket\Actions\AbstractAction::factoryByName($this->action);
    }
    public function scopeByStatus(\Illuminate\Database\Eloquent\Builder $query, string $status) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("status", $status);
    }
    public function scopeUpcoming(\Illuminate\Database\Eloquent\Builder $query) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereIn("status", [self::STATUS_SCHEDULED, self::STATUS_FAILED]);
    }
    public function scopeBySkipFlagsMask(\Illuminate\Database\Eloquent\Builder $query, int $skipFlags) : \Illuminate\Database\Eloquent\Builder
    {
        if($skipFlags == 0) {
            return $query->where("skip_flags", 0);
        }
        return $query->where("skip_flags", "!=", 0)->whereRaw("(skip_flags & " . $skipFlags . ") = skip_flags");
    }
    public function scopeForTicketId(\Illuminate\Database\Eloquent\Builder $query, int $ticketId) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("ticket_id", $ticketId);
    }
    public static function getByTicketInExecutionOrder($ticketId) : \Illuminate\Database\Eloquent\Collection
    {
        return (new static())::forTicketId($ticketId)->orderByExecution()->get();
    }
    public static function getTicketUpcomingActions($ticketId) : \Illuminate\Database\Eloquent\Collection
    {
        return (new static())::forTicketId($ticketId)->upcoming()->get();
    }
    public static function findTicketAction($ticketId, int $actionId) : TicketScheduledAction
    {
        return (new static())::where("id", $actionId)->forTicketId($ticketId)->first();
    }
    public function scopeOrderByExecution(\Illuminate\Database\Eloquent\Builder $query) : \Illuminate\Database\Eloquent\Builder
    {
        $actionLogicalOrder = array_keys((new \WHMCS\Support\Ticket\Actions\ActionsList())->getActions());
        return $query->orderBy("scheduled")->orderByRaw(sprintf("FIELD(`action`, \"%s\")", implode("\", \"", $actionLogicalOrder)));
    }
    protected function updateStatus($status) : \self
    {
        if(!in_array($status, self::SCHEDULED_ACTION_STATUSES)) {
            throw new \InvalidArgumentException($status . " is not a valid status");
        }
        $this->status = $status;
        $this->statusAt = \WHMCS\Carbon::now();
        return $this;
    }
    public function getSkipFlagsAttribute($value) : SkipFlagMask
    {
        if($value instanceof SkipFlagMask) {
            return $value;
        }
        return new SkipFlagMask($value ?? 0);
    }
    public function setSkipFlagsAttribute(SkipFlagMask $value) : void
    {
        $this->attributes["skip_flags"] = $value->mask();
    }
}

?>