<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Client;

class TicketsController
{
    public function tickets(\WHMCS\Http\Message\ServerRequest $request)
    {
        if(!function_exists("getStatusColour")) {
            require ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "ticketfunctions.php";
        }
        $adminUser = (new \WHMCS\Authentication\CurrentUser())->admin();
        $maxToDisplay = $adminUser->userPreferences["tableLengths"]["summaryTickets"] ?? 10;
        $userId = (int) $request->getAttribute("userId");
        $aInt = new \WHMCS\Admin("List Support Tickets");
        $aInt->valUserID($userId);
        $aInt->setHelpLink("Clients:Tickets Tab");
        $aInt->setClientSearchSubmitLocation(routePath("admin-client-search-submit-location", "tickets"))->setClientsProfilePresets($userId)->assertClientBoundary($userId)->setResponseType($aInt::RESPONSE_HTML_MESSAGE)->addHeadOutput(\WHMCS\View\Asset::jsInclude("AdminClientTicketTab.js?v=" . \WHMCS\View\Helper::getAssetVersionHash()));
        $csrfToken = generate_token("plain");
        $endOfLastMonth = \WHMCS\Carbon::now()->subMonth()->lastOfMonth()->endOfDay()->toDateTimeString();
        $endOfTwoMonthsAgo = \WHMCS\Carbon::now()->subMonth(2)->lastOfMonth()->endOfDay()->toDateTimeString();
        $firstOfThisMonth = \WHMCS\Carbon::now()->firstOfMonth()->toDateTimeString();
        $endOfLastYear = \WHMCS\Carbon::now()->subYear(1)->lastOfYear()->endOfDay()->toDateTimeString();
        $endOfTwoYearsAgo = \WHMCS\Carbon::now()->subYear(2)->lastOfYear()->endOfDay()->toDateTimeString();
        $firstOfThisYear = \WHMCS\Carbon::now()->firstOfYear()->toDateTimeString();
        $tickets = \WHMCS\Support\Ticket::userId($userId)->notMerged()->with("department", "flaggedAdmin");
        $aInt->content = view("admin.client.profile.tickets", ["csrfToken" => $csrfToken, "ticketPageLength" => $maxToDisplay, "tickets" => clone $tickets->orderBy("status", "desc")->limit($maxToDisplay)->get(), "userId" => $userId, "ticketCounts" => ["total" => clone $tickets->count(), "thisMonth" => clone $tickets->where("date", ">", $endOfLastMonth)->count(), "lastMonth" => clone $tickets->whereBetween("date", [$endOfTwoMonthsAgo, $firstOfThisMonth])->count(), "thisYear" => clone $tickets->where("date", ">", $endOfLastYear)->count(), "lastYear" => clone $tickets->whereBetween("date", [$endOfTwoYearsAgo, $firstOfThisYear])->count()]]);
        return $aInt->display();
    }
    public function close(\WHMCS\Http\Message\ServerRequest $request)
    {
        if(!function_exists("closeTicket")) {
            require ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "ticketfunctions.php";
        }
        $ticketIds = $request->request()->get("ticketIds");
        foreach ($ticketIds as $ticketId) {
            try {
                closeTicket($ticketId);
            } catch (\Exception $e) {
                logAdminActivity("Unable to close ticket. Ticket ID: " . $ticketId . " - Error: " . $e->getMessage());
            }
        }
        return new \WHMCS\Http\Message\JsonResponse(["success" => true]);
    }
    public function delete(\WHMCS\Http\Message\ServerRequest $request)
    {
        if(!function_exists("deleteTicket")) {
            require ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "ticketfunctions.php";
        }
        $ticketIds = $request->request()->get("ticketIds");
        foreach ($ticketIds as $ticketId) {
            try {
                deleteTicket($ticketId);
            } catch (\WHMCS\Exception\Fatal $e) {
                logAdminActivity("Unable to delete ticket. Ticket ID: " . $ticketId . " - Error: " . $e->getMessage());
            } catch (\Exception $e) {
            }
        }
        return new \WHMCS\Http\Message\JsonResponse(["success" => true]);
    }
    public function merge(\WHMCS\Http\Message\ServerRequest $request)
    {
        if(!function_exists("addTicketLog")) {
            require ROOTDIR . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "ticketfunctions.php";
        }
        $ticketIds = $request->request()->get("ticketIds");
        $userId = $request->get("userId");
        sort($ticketIds);
        $mainTicket = $ticketIds[0];
        unset($ticketIds[0]);
        try {
            \WHMCS\Support\Ticket::where("userid", $userId)->where("id", $mainTicket)->firstOrFail()->mergeOtherTicketsInToThis($ticketIds);
        } catch (\Exception $e) {
        }
        return new \WHMCS\Http\Message\JsonResponse(["success" => true]);
    }
}

?>