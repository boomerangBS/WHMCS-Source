<?php

namespace WHMCS\Table;

class TicketActionsTable extends AbstractTable
{
    protected function processData(\Illuminate\Support\Collection $scheduledActions) : void
    {
        if($scheduledActions->isEmpty()) {
            return NULL;
        }
        $this->data = $this->asRowsStruct($scheduledActions);
    }
    protected function getData(\WHMCS\Http\Message\ServerRequest $request) : \Illuminate\Support\Collection
    {
        $ticketId = $request->get("ticketId");
        if(!function_exists("validateAdminTicketAccess")) {
            require_once ROOTDIR . "/includes/ticketfunctions.php";
        }
        if(validateAdminTicketAccess($ticketId)) {
            throw new \WHMCS\Exception\User\PermissionsRequired();
        }
        $this->setColumns($request);
        return \WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction::getByTicketInExecutionOrder($ticketId);
    }
    public function asRowsStruct(\Illuminate\Support\Collection $scheduledActions) : array
    {
        return $scheduledActions->map(function ($action) {
            return (new TicketActionsTable\_\RowStructure($action))->toArray();
        })->toArray();
    }
}
namespace WHMCS\Table\TicketActionsTable\_;

class RowStructure
{
    private $scheduledAction;
    private $viewHelper;
    public function __construct(\WHMCS\Support\Ticket\ScheduledActions\TicketScheduledAction $scheduledAction)
    {
        $this->scheduledAction = $scheduledAction;
        $this->viewHelper = new View\Helper();
    }
    public function toArray() : array
    {
        return ["id" => $this->scheduledAction->id, "editIcon" => $this->attributeEditIcon(), "statusIcon" => $this->attributeStatusIcon(), "actionName" => $this->attributeActionName(), "actionDetail" => $this->attributeActionDetail(), "scheduled" => $this->attributeScheduled(), "createdAdmin" => $this->attributeCreatedAdmin(), "actionStatus" => $this->attributeActionStatus(), "status" => $this->attributeStatus(), "edit" => $this->attributeEdit(), "DT_RowId" => "scheduledAction" . $this->scheduledAction->id];
    }
    protected function attributeEditIcon()
    {
        return $this->viewHelper->iconFaInteractWithActionIcon($this->scheduledAction->status);
    }
    protected function attributeStatusIcon()
    {
        return $this->viewHelper->iconFaStatusIcon($this->scheduledAction->status);
    }
    protected function attributeCreatedAdmin()
    {
        $createdAdmin = $this->scheduledAction->createdAdmin;
        return !is_null($createdAdmin) ? $createdAdmin->fullName : "";
    }
    protected function attributeActionStatus()
    {
        return (new \WHMCS\Support\Ticket\ScheduledActions\View\ScheduledActions())->translate()->setScheduledAction($this->scheduledAction)->status();
    }
    protected function attributeStatus()
    {
        return sprintf("<i class=\"fas %s\"></i>&nbsp;%s", $this->attributeStatusIcon(), $this->attributeActionStatus());
    }
    protected function attributeEdit()
    {
        $controls = [];
        $controls[] = sprintf("<a href=\"#\" data-action-id=\"%s\"><i class=\"fal %s\"></i></a>", $this->scheduledAction->id, $this->attributeEditIcon());
        return implode("", $controls);
    }
    protected function attributeScheduled()
    {
        return \WHMCS\Carbon::parse($this->scheduledAction->scheduled)->toAdminDateTimeFormat();
    }
    protected function attributeActionDetail()
    {
        try {
            return $this->scheduledAction->getAction()->detailString();
        } catch (\Throwable $e) {
        }
        return "";
    }
    protected function attributeActionName()
    {
        return $this->scheduledAction->getAction()->displayName();
    }
}
namespace WHMCS\Table\TicketActionsTable\_\View;

class Helper
{
    protected $iconFaDefaultStatus = "fa-question-circle";
    protected $iconFaStatusMap;
    protected $iconFaDefaultInteractWithAction = "fa-edit";
    protected $iconFaInteractWithActionMap;
    public function iconFaStatusIcon($status)
    {
        return $this->iconFaStatusMap[$status] ?? $this->iconFaDefaultStatus;
    }
    public function iconFaInteractWithActionIcon($status)
    {
        return $this->iconFaInteractWithActionMap[$status] ?? $this->iconFaDefaultInteractWithAction;
    }
}

?>