<?php

namespace WHMCS\Table;

class TicketTable extends AbstractTable
{
    protected $castColumns = [];
    protected function processData(\Illuminate\Database\Eloquent\Collection $tickets) : void
    {
        if(!function_exists("getStatusColour")) {
            require ROOTDIR . "/includes/ticketfunctions.php";
        }
        if(!empty($tickets)) {
            $assetHelper = \DI::make("asset");
            $dateCellFmt = "<span class=\"hidden\">%s</span>%s";
            foreach ($tickets as $ticket) {
                $idShort = ltrim($ticket->id, "0");
                $ticketLink = $assetHelper->getWebRoot() . "/" . $ticket->getLink();
                $columnData = ["select" => sprintf("<input type=\"checkbox\" name=\"id[]\" class=\"ticket-checkbox\" value=\"%s\">", $ticket->id), "date" => sprintf($dateCellFmt, $ticket->date->timestamp, $ticket->date->toAdminDateFormat()), "priority" => sprintf("<img src=\"images/%spriority.gif\" \"width=\"16\" height=\"16\" class=\"absmiddle\"alt=\"%s\"/>", strtolower($ticket->priority), \AdminLang::trans("status." . strtolower($ticket->priority))), "status" => getStatusColour($ticket->status), "department" => $ticket->department->name . ($ticket->flaggedAdmin ? " (" . $ticket->flaggedAdmin->fullName . ")" : ""), "title" => sprintf("<a href=\"%s\"> #%s - %s</a>", $ticketLink, $ticket->ticketNumber, $ticket->title), "lastreply" => sprintf($dateCellFmt, $ticket->lastReply->timestamp, $ticket->lastReply->diffForHumans())];
                $dataRow = [];
                foreach ($this->getColumns() as $column) {
                    $dataRow[$column] = $columnData[$column] ?? "";
                }
                $dataRow["DT_RowAttr"] = ["id" => "ticket" . $idShort];
                $this->data[] = $dataRow;
            }
        }
    }
    protected function getData(\WHMCS\Http\Message\ServerRequest $request) : \Illuminate\Database\Eloquent\Collection
    {
        $admin = (new \WHMCS\Authentication\CurrentUser())->admin();
        $length = $request->get("length", 10);
        $adminPreferences = $admin->userPreferences;
        if(empty($adminPreferences["tableLengths"])) {
            $adminPreferences["tableLengths"] = [];
        }
        if(empty($adminPreferences["tableLengths"]["default"])) {
            $adminPreferences["tableLengths"]["default"] = 10;
        }
        $adminPreferences["tableLengths"]["summaryTickets"] = $length;
        $admin->userPreferences = $adminPreferences;
        $admin->save();
        $this->setColumns($request);
        $collection = \WHMCS\Support\Ticket::userId($request->get("clientId"))->notMerged()->with("department", "flaggedAdmin");
        $this->totalData = $collection->count();
        if(!empty($request->get("search")["value"])) {
            $collection->where("title", "like", "%" . $request->get("search")["value"] . "%");
        }
        $this->totalFiltered = $collection->count();
        foreach ($request->get("order") as $orderBy) {
            $column = $this->columns[$orderBy["column"]];
            $collection = $collection->orderBy(in_array($column, $this->castColumns) ? \WHMCS\Database\Capsule::raw("CAST(" . $column . " as CHAR)") : $column, preg_match("/^(asc|desc)\$/", $orderBy["dir"]) ? $orderBy["dir"] : "asc");
        }
        $collection = $collection->offset($request->get("start"))->limit($length);
        return $collection->get();
    }
}

?>