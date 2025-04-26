<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Ticket;

class Watchers extends \WHMCS\Model\AbstractModel
{
    protected $table = "tblticket_watchers";
    public $timestamps = true;
    public function createTable($drop = false)
    {
        $schemaBuilder = \WHMCS\Database\Capsule::schema();
        if($drop) {
            $schemaBuilder->dropIfExists($this->getTable());
        }
        if(!$schemaBuilder->hasTable($this->getTable())) {
            $schemaBuilder->create($this->getTable(), function ($table) {
                $table->increments("id");
                $table->integer("ticket_id", false, true);
                $table->integer("admin_id", false, true);
                $table->timestamps();
                $table->unique(["ticket_id", "admin_id"], "admin_ticket_unique");
            });
        }
    }
    public function scopeOfTicket(\Illuminate\Database\Eloquent\Builder $query, $ticketId)
    {
        return $query->whereTicketId($ticketId);
    }
    public function scopeByAdmin(\Illuminate\Database\Eloquent\Builder $query, $adminId)
    {
        return $query->whereAdminId($adminId);
    }
}

?>