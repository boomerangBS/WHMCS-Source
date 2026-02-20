<?php

namespace WHMCS\Admin\Support;

class SupportRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\AdminProviderTrait;
    public function getRoutes()
    {
        return ["/admin/support" => [["method" => ["POST"], "name" => "admin-support-ticket-open-additional-data", "path" => "/ticket/open/client/{clientId:\\d+}/additional/data", "authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["Open New Ticket"])->requireCsrfToken();
        }, "handle" => ["WHMCS\\Admin\\Support\\SupportController", "getAdditionalData"]], ["method" => ["POST"], "name" => "admin-support-ticket-related-list", "path" => "/ticket/{ticketId:\\d+}/client/{clientId:\\d+}/services", "authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["View Support Ticket"])->requireCsrfToken();
        }, "handle" => ["WHMCS\\Admin\\Support\\SupportController", "getClientServices"]], ["method" => ["POST"], "name" => "admin-support-ticket-set-related-service", "path" => "/ticket/{ticketId:\\d+}/client/{clientId:\\d+}/services/save", "authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["View Support Ticket"])->requireCsrfToken();
        }, "handle" => ["WHMCS\\Admin\\Support\\SupportController", "setRelatedService"]], ["method" => ["POST"], "name" => "admin-support-ticket-create-scheduled-action", "path" => "/ticket/{ticketId:\\d+}/actions/create", "authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["Create Scheduled Ticket Actions", "View Support Ticket"])->requireCsrfToken();
        }, "handle" => ["WHMCS\\Support\\Ticket\\ScheduledActions\\TicketScheduledActionController", "createAction"]], ["method" => ["GET"], "name" => "admin-support-ticket-view-scheduled-action", "path" => "/ticket/{ticketId:\\d+}/actions/{actionId:\\d+}", "authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["View Support Ticket", "View Scheduled Ticket Actions"]);
        }, "handle" => ["WHMCS\\Support\\Ticket\\ScheduledActions\\TicketScheduledActionController", "viewAction"]], ["method" => ["POST"], "name" => "admin-support-ticket-cancel-scheduled-action", "path" => "/ticket/{ticketId:\\d+}/actions/{actionId:\\d+}/cancel", "authentication" => "admin", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAllPermission(["View Support Ticket", "Cancel Scheduled Ticket Actions"])->requireCsrfToken();
        }, "handle" => ["WHMCS\\Support\\Ticket\\ScheduledActions\\TicketScheduledActionController", "cancelAction"]]]];
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "admin-support-";
    }
}

?>