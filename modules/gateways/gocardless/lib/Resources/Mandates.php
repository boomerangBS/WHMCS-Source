<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\GoCardless\Resources;

class Mandates extends AbstractResource
{
    const STATUSES = ["pending_customer_approval" => "Pending Customer Approval", "pending_submission" => "Pending Submission", "submitted" => "Submitted", "active" => "Active", "failed" => "Failed", "cancelled" => "Cancelled", "expired" => "Expired"];
    public function cancelled(array $event)
    {
        $mandateId = $event["links"]["mandate"];
        $client = $this->getClientFromMandate($mandateId);
        if($client) {
            $payMethod = $client->payMethods()->where("gateway_name", "gocardless")->first();
            if($payMethod && $payMethod->payment->isRemoteBankAccount() && $payMethod->payment->getRemoteToken() == $mandateId) {
                $payMethod->delete();
            }
        }
    }
    public function created(array $event)
    {
        $mandateId = $event["links"]["mandate"];
        $client = $this->getClientFromMandate($mandateId);
        if(!$client) {
            logTransaction($this->params["paymentmethod"], $event, "No Client Found for Mandate", $this->params);
            return false;
        }
        $payMethod = $client->payMethods()->where("gateway_name", "gocardless")->first();
        if(!$payMethod) {
            $payMethod = \WHMCS\Payment\PayMethod\Adapter\RemoteBankAccount::factoryPayMethod($client, $client);
        }
        $payMethod->payment->setRemoteToken($mandateId);
        $payMethod->payment->save();
        try {
            $this->client->put("mandates/" . $mandateId, ["json" => ["mandates" => ["metadata" => ["client_id" => (string) (string) $client->id]]]]);
        } catch (\Exception $e) {
        }
    }
    public function failed(array $event)
    {
        $this->cancelled($event);
    }
    public function reinstated(array $event)
    {
        $mandateId = $event["links"]["mandate"];
        $client = $this->getClientFromMandate($mandateId);
        if(is_null($client)) {
            return NULL;
        }
        try {
            $payMethod = $client->payMethods()->where("gateway_name", "gocardless")->first();
            if(!$payMethod) {
                $payMethod = \WHMCS\Payment\PayMethod\Adapter\RemoteBankAccount::factoryPayMethod($client, $client);
            }
            $payMethod->payment->setRemoteToken($mandateId);
            $payMethod->payment->save();
        } catch (\Exception $e) {
        }
    }
    public function replaced(array $event)
    {
        $mandateId = $event["links"]["mandate"];
        $client = $this->getClientFromMandate($mandateId);
        if($client) {
            $newMandateId = $event["links"]["new_mandate"];
            $payMethod = $client->payMethods()->where("gateway_name", "gocardless")->first();
            if(!$payMethod) {
                $payMethod = \WHMCS\Payment\PayMethod\Adapter\RemoteBankAccount::factoryPayMethod($client, $client);
            }
            $payMethod->payment->setRemoteToken($newMandateId);
            $payMethod->payment->save();
        }
    }
    public function defaultAction(array $event)
    {
        logTransaction($this->params["paymentmethod"], $event, "Mandate Notification", $this->params);
    }
    protected function getClientFromMandate($mandateId)
    {
        $client = NULL;
        try {
            $mandateResponse = json_decode($this->client->get("mandates/" . $mandateId), true);
            if(isset($mandateResponse["mandates"]) && is_array($mandateResponse["mandates"])) {
                $mandateResponse = array_shift($mandateResponse);
            }
            if(isset($mandateResponse["metadata"]["client_id"])) {
                $clientId = $mandateResponse["metadata"]["client_id"];
                $client = \WHMCS\User\Client::find($clientId);
            }
            if(!$client && isset($mandateResponse["links"]["customer"])) {
                $customerId = $mandateResponse["links"]["customer"];
                unset($mandateResponse);
                $customerResponse = json_decode($this->client->get("customers/" . $customerId), true);
                if(isset($customerResponse["customers"]["email"])) {
                    $email = $customerResponse["customers"]["email"];
                    $client = \WHMCS\User\Client::where("email", $email)->first();
                }
                unset($customerResponse);
            }
        } catch (\Exception $e) {
            return $client;
        }
        return $client;
    }
}

?>