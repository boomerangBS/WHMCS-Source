<?php

namespace WHMCS\Updater\Version;

class Version8110beta1 extends IncrementalVersion
{
    protected $updateActions = ["addCancelInvoicePermissionToAppropriateRoles", "updateCreditCardEmailTemplates"];
    public function __construct(\WHMCS\Version\SemanticVersion $version)
    {
        parent::__construct($version);
        $this->filesToRemove[] = ROOTDIR . "vendor/whmcs/whmcs-foundation/" . "lib/Admin/Wizard/Steps/GettingStarted/CreditCard.php";
    }
    public function updateCreditCardEmailTemplates()
    {
        $targetTemplates = [["name" => "Credit Card Invoice Created", "subject" => "Customer Invoice", "md5" => "fa778b91d4c797a0a1d89f619b2c468a", "newSubject" => "Invoice Generated", "newMessage" => "<p>Hi {\$client_name},</p>\\r\\n<p>We generated an invoice for you on {\$invoice_date_created}:</p>\\r\\n<p><strong>Invoice Date:</strong> {\$invoice_date_created}<br /><strong>Invoice #</strong>{\$invoice_num}<br /><strong>Amount Due:</strong> {\$invoice_total}<br /><strong>Due Date:</strong> {\$invoice_date_due}</p>\\r\\n<p><strong>Invoice Items:</strong><br />{\$invoice_html_contents}</p>\\r\\n<p>{if \$invoice_auto_capture_available}We will process payment using your selected payment method ({\$invoice_pay_method_display_name}) on {\$invoice_next_payment_attempt_date}. To pay with a different payment method, log in at {\$invoice_link}. Click <strong>Pay Now</strong> and follow the displayed instructions.{else}To pay your invoice, log in at {\$invoice_link}. Click <strong>Pay Now</strong> and follow the displayed instructions.{/if}</p>\\r\\n<p>{\$signature}</p>"], ["name" => "Credit Card Payment Due", "subject" => "Credit Card Payment Due", "md5" => "05c37cde2867b9530fe2352ead5a29cb", "newSubject" => "Invoice Payment Due", "newMessage" => "<p>Hi {\$client_name},</p>\\r\\n<p>Payment for invoice #{\$invoice_num} is due on {\$invoice_next_payment_attempt_date}.</p>\\r\\n<p><strong>Invoice Date:</strong> {\$invoice_date_created}<br /><strong>Invoice #</strong>{\$invoice_num}<br /><strong>Amount Due:</strong> {\$invoice_total}<br /><strong>Due Date:</strong> {\$invoice_date_due}<br /><strong>Status:</strong> {\$invoice_status}</p>\\r\\n<p>We cannot automatically process payment because you do not have a payment method on file. To add a payment method to your account or pay manually, log in at {\$invoice_link}.</p>\\r\\n<p>{\$signature}</p>"], ["name" => "Credit Card Payment Failed", "subject" => "Credit Card Payment Failed", "md5" => "f4e657fff0e1de8eae3c4a57f7d8059f", "newSubject" => "Invoice Payment Failed", "newMessage" => "<p>Hi {\$client_name},</p>\\r\\n<p>We attempted to process payment for invoice #{\$invoice_num} but your selected payment method failed.</p>\\r\\n<p><strong>Invoice Date:</strong> {\$invoice_date_created}<br /><strong>Invoice #</strong>{\$invoice_num}<br /><strong>Amount Due:</strong> {\$invoice_total}<br /><strong>Due Date:</strong> {\$invoice_date_due}<br /><strong>Status:</strong> {\$invoice_status}</p>\\r\\n<p>You must log in at {\$invoice_link} and pay your invoice manually. During the payment process, you can also update your account\\'s preferred payment method.</p>\\r\\n<p>{\$signature}</p>"], ["name" => "Credit Card Payment Pending", "subject" => "Credit Card Payment Pending", "md5" => "02450cfa118133e88a82b75048f653ff", "newSubject" => "Invoice Payment Pending", "newMessage" => "<p>Hi {\$client_name},</p>\\r\\n<p>Payment is currently pending for invoice #{\$invoice_num}.</p>\\r\\n<p><strong>Invoice Date:</strong> {\$invoice_date_created}<br /><strong>Invoice #</strong>{\$invoice_num}<br /><strong>Amount Due:</strong> {\$invoice_total}<br /><strong>Due Date:</strong> {\$invoice_date_due}<br /><strong>Status:</strong> {\$invoice_status}</p>\\r\\n<p>We will process payment using your selected payment method ({\$invoice_pay_method_display_name}).</p>\\r\\n<p>Log in to your account with us to review your current invoices and payment history.</p>\\r\\n<p>{\$signature}</p>"]];
        $existingTemplateCollection = \WHMCS\Database\Capsule::table("tblemailtemplates")->where("language", "")->whereIn("name", ["Credit Card Invoice Created", "Credit Card Payment Due", "Credit Card Payment Failed", "Credit Card Payment Pending"])->get(["id", "name", "subject", "message"]);
        foreach ($targetTemplates as $targetTemplate) {
            $updateArray = [];
            $existingTemplateData = $existingTemplateCollection->where("name", $targetTemplate["name"])->first();
            if(md5($existingTemplateData->message) == $targetTemplate["md5"]) {
                $updateArray["message"] = \WHMCS\Database\Capsule::raw("'" . $targetTemplate["newMessage"] . "'");
                if($existingTemplateData->subject == $targetTemplate["subject"]) {
                    $updateArray["subject"] = $targetTemplate["newSubject"];
                }
            }
            if(!empty($updateArray)) {
                \WHMCS\Database\Capsule::table("tblemailtemplates")->where("id", $existingTemplateData->id)->update($updateArray);
            }
        }
    }
    public function addCancelInvoicePermissionToAppropriateRoles() : \self
    {
        $cancelInvoicePermission = \WHMCS\User\Admin\Permission::findId("Cancel Invoice");
        $manageInvoicePermissionId = \WHMCS\User\Admin\Permission::findId("Manage Invoice");
        $existingRolesWithManageInvoices = \WHMCS\Database\Capsule::table("tbladminperms")->where("permid", $manageInvoicePermissionId)->pluck("roleid")->toArray();
        $existingRolesWithViewInvoices = \WHMCS\Database\Capsule::table("tbladminperms")->where("permid", $cancelInvoicePermission)->pluck("roleid")->toArray();
        if($existingRolesWithManageInvoices) {
            $newValues = [];
            foreach ($existingRolesWithManageInvoices as $existingRolesWithManageInvoice) {
                if(in_array($existingRolesWithManageInvoice, $existingRolesWithViewInvoices)) {
                } else {
                    $newValues[] = ["roleid" => $existingRolesWithManageInvoice, "permid" => $cancelInvoicePermission];
                }
            }
            if(count($newValues)) {
                \WHMCS\Database\Capsule::table("tbladminperms")->insert($newValues);
            }
        }
        return $this;
    }
}

?>