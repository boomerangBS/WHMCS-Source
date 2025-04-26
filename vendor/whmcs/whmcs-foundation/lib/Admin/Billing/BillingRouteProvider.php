<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Billing;

class BillingRouteProvider implements \WHMCS\Route\Contracts\DeferredProviderInterface
{
    use \WHMCS\Route\AdminProviderTrait;
    public function getRoutes()
    {
        $routes = ["/admin/billing" => ["attributes" => ["authentication" => "admin", "authorization" => function () {
            return new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization();
        }], ["method" => ["POST"], "name" => "admin-billing-invoice-new", "path" => "/invoice/new", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "newInvoice"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->setRequireAllPermission(["Create Invoice"]);
        }], ["method" => ["POST"], "name" => "admin-billing-invoice-create", "path" => "/invoice/create", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "createInvoice"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken()->setRequireAllPermission(["Create Invoice"]);
        }], ["method" => ["POST"], "name" => "admin-billing-invoice-email-send", "path" => "/invoice/{invoiceId:\\d+}/email", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "sendEmail"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken()->setRequireAnyPermission(["Manage Invoice", "View Invoice"]);
        }], ["method" => ["GET"], "name" => "admin-billing-view-invoice", "path" => "/invoice/{invoiceId:\\d+}", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->setRequireAnyPermission(["Manage Invoice", "View Invoice"]);
        }, "authentication" => "admin", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "viewInvoice"]], ["method" => ["GET"], "name" => "admin-billing-view-invoice-tooltip", "path" => "/invoice/{invoiceId:\\d+}/tooltip/{token}", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken()->setRequireAnyPermission(["Manage Invoice", "View Invoice"]);
        }, "authentication" => "admin", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "viewInvoiceTooltip"]], ["method" => ["POST"], "name" => "admin-billing-check-transaction-id", "path" => "/check-transaction", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken()->setRequireAnyPermission(["Manage Invoice", "View Invoice"]);
        }, "authentication" => "admin", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "checkTransactionId"]], ["method" => ["POST"], "name" => "admin-billing-view-invoice-add-payment", "path" => "/invoice/{invoiceId:\\d+}/add-payment", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken()->setRequireAllPermission(["Add Transaction"]);
        }, "authentication" => "admin", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "addInvoicePayment"]], ["method" => ["POST"], "name" => "admin-billing-view-invoice-change-gateway", "path" => "/invoice/{invoiceId:\\d+}/change-gateway", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken()->setRequireAnyPermission(["Manage Invoice", "View Invoice"]);
        }, "authentication" => "admin", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "changeGateway"]], ["method" => ["POST"], "name" => "admin-billing-invoice-add-credit", "path" => "/invoice/{invoiceId:\\d+}/add-credit", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken()->setRequireAnyPermission(["Manage Invoice", "View Invoice"]);
        }, "authentication" => "admin", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "addCredit"]], ["method" => ["POST"], "name" => "admin-billing-invoice-remove-credit", "path" => "/invoice/{invoiceId:\\d+}/remove-credit", "authorization" => function () {
            return (new \WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization())->requireCsrfToken()->setRequireAnyPermission(["Manage Invoice", "View Invoice"]);
        }, "authentication" => "admin", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "removeCredit"]], ["method" => ["POST"], "name" => "admin-billing-view-invoice-refund", "path" => "/invoice/{invoiceId:\\d+}/refund", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "viewInvoiceRefundPayment"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken()->setRequireAnyPermission(["Refund Invoice Payments"]);
        }], ["method" => ["GET", "POST"], "name" => "admin-billing-offline-cc-form", "path" => "/offline-cc/invoice/{invoice_id:\\d+}", "handle" => ["WHMCS\\Admin\\Billing\\OfflineCcController", "getForm"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->setRequireAllPermission(["Offline Credit Card Processing"]);
        }], ["method" => ["POST"], "name" => "admin-billing-offline-cc-decrypt", "path" => "/offline-cc/invoice/{invoice_id:\\d+}/decrypt_card", "handle" => ["WHMCS\\Admin\\Billing\\OfflineCcController", "decryptCardData"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken()->setRequireAllPermission(["Offline Credit Card Processing"]);
        }], ["method" => ["POST"], "name" => "admin-billing-offline-cc-apply-transaction", "path" => "/offline-cc/invoice/{invoice_id:\\d+}/apply_transaction", "handle" => ["WHMCS\\Admin\\Billing\\OfflineCcController", "applyTransaction"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken()->setRequireAllPermission(["Offline Credit Card Processing"]);
        }], ["method" => ["POST"], "name" => "admin-billing-gateway-balance-totals", "path" => "/gateway/balance/totals", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "gatewayBalancesTotals"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->requireCsrfToken()->setRequireAllPermission(["View Gateway Balances"]);
        }], ["method" => ["POST"], "name" => "admin-billing-transaction-information", "path" => "/transaction/{id:\\d+}/information", "handle" => ["WHMCS\\Admin\\Billing\\BillingController", "transactionInformation"], "authorization" => function (\WHMCS\Admin\ApplicationSupport\Route\Middleware\Authorization $authz) {
            return $authz->setRequireAllPermission(["List Transactions"]);
        }]]];
        return $routes;
    }
    public function getDeferredRoutePathNameAttribute()
    {
        return "admin-billing-";
    }
}

?>