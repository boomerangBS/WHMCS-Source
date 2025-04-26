<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Support\Ticket;

class TicketImportNotification extends \Illuminate\Database\Eloquent\Relations\Pivot
{
    protected $table = "tblticketpendingimports";
    public $timestamps = false;
    public function createTable($drop = false)
    {
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($drop) {
            $schemaBuilder->dropIfExists($this->getTable());
        }
        if(!$schemaBuilder->hasTable($this->getTable())) {
            $schemaBuilder->create($this->getTable(), function ($table) {
                $table->integer("ticket_id");
                $table->integer("ticketmaillog_id");
                $table->index("ticket_id", "ticket_id_idx");
                $table->unique(["ticketmaillog_id", "ticket_id"], "ticketmaillog_id_ticket_id");
            });
        }
    }
    public function ticket() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo("WHMCS\\Support\\Ticket", "ticket_id", "id", "ticket");
    }
    public function importLog() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo("WHMCS\\Log\\TicketImport", "ticketmaillog_id", "id", "importLog");
    }
    public function scopeTicketId(\Illuminate\Database\Eloquent\Builder $query, int $ticketId) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("ticket_id", $ticketId);
    }
    public function scopeImportLogId(\Illuminate\Database\Eloquent\Builder $query, int $importLogId) : \Illuminate\Database\Eloquent\Builder
    {
        return $query->where("ticketmaillog_id", $importLogId);
    }
    public static function storeEntry(\WHMCS\Log\TicketImport $ticketImport) : void
    {
        $ticket = $ticketImport->getTicket();
        if($ticket && 0 < $ticketImport->id && $ticketImport->isPending()) {
            TicketImportNotification::insertOrIgnore(["ticketmaillog_id" => $ticketImport->id, "ticket_id" => $ticket->id]);
        }
    }
}

?>