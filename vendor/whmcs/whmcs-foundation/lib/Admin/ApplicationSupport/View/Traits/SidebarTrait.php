<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

namespace WHMCS\Admin\ApplicationSupport\View\Traits;

// Decoded file for php version 72.
trait SidebarTrait
{
    protected $sidebarName = "";
    protected $sidebarNameOptions = ["support", "config", "home", "clients", "utilities", "billing", "orders", "addonmodules", "reports", "logs"];
    public abstract function getAdminUser();
    public function getSidebarName()
    {
        return $this->sidebarName;
    }
    public function setSidebarName($name)
    {
        if(in_array($name, $this->sidebarNameOptions)) {
            $this->sidebarName = $name;
        } elseif(empty($name)) {
            $this->sidebarName = "";
        }
        return $this;
    }
    public function isSidebarMinimized()
    {
        return (bool) \WHMCS\Cookie::get("MinSidebar");
    }
    public function getSidebarVariables()
    {
        $sidebarVariables = [];
        $ticketStats = NULL;
        $appConfig = \DI::make("config");
        $disableAdminTicketPageCounts = (bool) $appConfig->disable_admin_ticket_page_counts;
        if($this->getSidebarName() == "support") {
            $ticketStats = localApi("GetTicketCounts", ["includeCountsByStatus" => !$disableAdminTicketPageCounts]);
            $ticketCounts = [];
            $ticketStatuses = \WHMCS\Database\Capsule::table("tblticketstatuses")->orderBy("sortorder")->pluck("title")->all();
            foreach ($ticketStatuses as $status) {
                $normalisedStatus = preg_replace("/[^a-z0-9]/", "", strtolower($status));
                $ticketCounts[] = ["title" => $status, "count" => isset($ticketStats["status"][$normalisedStatus]["count"]) ? $ticketStats["status"][$normalisedStatus]["count"] : 0];
            }
            $departments = [];
            $departmentsData = \WHMCS\Support\Department::whereIn("id", $ticketStats["filteredDepartments"])->orderBy("order")->get(["id", "name"])->all();
            foreach ($departmentsData as $department) {
                $departments[] = ["id" => $department->id, "name" => $department->name];
            }
            $sidebarVariables = ["ticketsallactive" => $ticketStats["allActive"], "ticketsawaitingreply" => $ticketStats["awaitingReply"], "ticketsflagged" => $ticketStats["flaggedTickets"], "ticketcounts" => $ticketCounts, "ticketstatuses" => $ticketCounts, "ticketdepts" => $departments];
        }
        if($this->getSidebarName() == "home") {
            $updater = new \WHMCS\Installer\Update\Updater();
            $licensing = \DI::make("license");
            $sidebarVariables["licenseinfo"] = ["registeredname" => $licensing->getRegisteredName(), "productname" => $licensing->getProductName(), "expires" => $licensing->getExpiryDate(), "currentversion" => \App::self()->getVersion()->getCasual(), "latestversion" => $updater->getLatestVersion()->getCasual(), "updateavailable" => $updater->isUpdateAvailable()];
        }
        if($this->getSidebarName() === "logs") {
            $sidebarVariables["dateTimeNowFormatted"] = \WHMCS\Carbon::now()->toAdminDateTimeFormat();
        }
        return $sidebarVariables;
    }
}

?>