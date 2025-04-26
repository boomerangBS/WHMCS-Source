<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler;

class DisputeHandler extends AbstractHandler
{
    const NUMBER_OF_PAGES = 10;
    const ITEMS_PER_PAGE = 50;
    const MERCHANDISE_OR_SERVICE_NOT_RECEIVED = "merchandise_or_service_not_received";
    const MERCHANDISE_OR_SERVICE_NOT_AS_DESCRIBED = "merchandise_or_service_not_as_described";
    const UNAUTHORISED = "unauthorised";
    const CREDIT_NOT_PROCESSED = "credit_not_processed";
    const DUPLICATE_TRANSACTION = "duplicate_transaction";
    const INCORRECT_AMOUNT = "incorrect_amount";
    const PAYMENT_BY_OTHER_MEANS = "payment_by_other_means";
    const CANCELED_RECURRING_BILLING = "canceled_recurring_billing";
    const OTHER = "other";
    const PROOF_OF_FULFILLMENT = "proof_of_fulfillment";
    const PROOF_OF_REFUND = "proof_of_refund";
    const PROOF_OF_SHIPMENT = "proof_of_shipment";
    const VISIBLE_TYPES = NULL;
    public function listDisputes() : \WHMCS\Billing\Payment\Dispute\DisputeCollection
    {
        $disputeCollection = new \WHMCS\Billing\Payment\Dispute\DisputeCollection();
        $transientDataCollection = collect(json_decode(\WHMCS\TransientData::getInstance()->retrieve($this->getTransientDataKey()), true));
        foreach ($this->getBulkDisputeCollection() as $disputeItem) {
            $transactionId = $this->getDisputeTransactionId($disputeItem->dispute_id, $transientDataCollection);
            if(empty($transactionId)) {
            } else {
                $transientDataCollection->push(["disputeId" => $disputeItem->dispute_id, "transactionId" => $transactionId]);
                $disputeCollection->push($this->buildDisputeObject($disputeItem, $transactionId)->setGateway($this->module->getLoadedModule())->setIsClosable(in_array($disputeItem->status, ["WAITING_FOR_BUYER_RESPONSE", "WAITING_FOR_SELLER_RESPONSE"])));
            }
        }
        \WHMCS\TransientData::getInstance()->store($this->getTransientDataKey(), json_encode($transientDataCollection->toArray()), 31536000);
        return $disputeCollection;
    }
    public function fetchDispute($disputeId) : \WHMCS\Billing\Payment\Dispute
    {
        $api = $this->api();
        $showDisputeDetailsResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\ShowDisputeDetailsRequest($api))->setIdentifier($disputeId));
        $paymentDispute = $this->buildDisputeObject($showDisputeDetailsResponse)->setGateway($this->module->getLoadedModule())->setIsClosable(in_array($showDisputeDetailsResponse->status, ["WAITING_FOR_BUYER_RESPONSE", "WAITING_FOR_SELLER_RESPONSE"]))->setManageHref(sprintf("%s/resolutioncenter/%s", $this->env()->webURL, $showDisputeDetailsResponse->dispute_id));
        $evidence = [];
        foreach ($this->getDisputeEvidenceTypes(strtolower($showDisputeDetailsResponse->reason)) as $typeKey) {
            $paymentDispute->setEvidenceType($typeKey, "custom")->setCustomData($typeKey, $this->getDisputeCustomData($typeKey));
            $evidence[] = ["name" => $typeKey, "value" => NULL];
        }
        $paymentDispute->setVisibleTypes(self::VISIBLE_TYPES)->setEvidence(array_merge($this->parseResponseEvidence($showDisputeDetailsResponse->evidences), $evidence));
        return $paymentDispute;
    }
    public function closeDispute($disputeId) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AcceptClaimResponse
    {
        $api = $this->api();
        return $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\AcceptClaimRequest($api))->setIdentifier($disputeId)->setNote("Claim accepted via WHMCS."));
    }
    private function buildDisputeObject($disputeItem = NULL, string $transactionId) : \WHMCS\Billing\Payment\Dispute
    {
        return \WHMCS\Billing\Payment\Dispute::factory($disputeItem->dispute_id, $disputeItem->dispute_amount->value, $disputeItem->dispute_amount->currency_code, $transactionId ?? $disputeItem->disputed_transactions[0]->seller_transaction_id, \WHMCS\Carbon::parse($disputeItem->create_time), \WHMCS\Carbon::parse($disputeItem->seller_response_due_date ?? $disputeItem->buyer_response_due_date), strtolower($disputeItem->reason), strtolower($disputeItem->status));
    }
    private function getBulkDisputeCollection() : \Illuminate\Support\Collection
    {
        $api = $this->api();
        $nextPageToken = NULL;
        $responseItems = collect();
        for ($i = 0; $i < self::NUMBER_OF_PAGES; $i++) {
            $listDisputesResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\ListDisputesRequest($api))->setNextPageToken($nextPageToken)->withPerPage(self::ITEMS_PER_PAGE));
            $responseItems = $responseItems->merge($listDisputesResponse->items);
            $nextLink = collect($listDisputesResponse->links)->where("rel", "next")->first();
            if(is_null($nextLink)) {
                break;
            }
            parse_str(parse_url($nextLink->href, PHP_URL_QUERY), $queryParams);
            $nextPageToken = $queryParams["next_page_token"];
        }
        return $responseItems;
    }
    private function getDisputeTransactionId($disputeId = NULL, $transientDataCollection) : \Illuminate\Support\Collection
    {
        $transientDataItem = !is_null($transientDataCollection) ? $transientDataCollection->firstWhere("disputeId", $disputeId) : NULL;
        if(!is_null($transientDataItem)) {
            return $transientDataItem["transactionId"];
        }
        $api = $this->api();
        $showDisputeDetailsResponse = $api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\ShowDisputeDetailsRequest($api))->setIdentifier($disputeId));
        return $showDisputeDetailsResponse->getTransactionId();
    }
    private function generateEvidenceString($evidence)
    {
        $evidenceDetails = [["name" => "date", "string" => \WHMCS\Carbon::parse($evidence->date)->toAdminDateFormat()]];
        if(!empty($evidence->action_info)) {
            $evidenceDetails[] = ["name" => "action", "string" => $evidence->action_info->action];
        }
        if(!empty($evidence->evidence_info->tracking_info)) {
            $evidenceDetails[] = ["name" => "carrierName", "string" => $evidence->evidence_info->tracking_info[0]->carrier_name];
            $evidenceDetails[] = ["name" => "trackingNumber", "string" => $evidence->evidence_info->tracking_info[0]->tracking_number];
        }
        if(!empty($evidence->evidence_info->refund_ids)) {
            $evidenceDetails[] = ["name" => "refundIds", "string" => implode(",", collect($evidence->evidence_info->refund_ids)->pluck("refund_id")->all())];
        }
        if(!empty($evidence->documents)) {
            $evidenceDetails[] = ["name" => "documents", "string" => implode(",", collect($evidence->documents)->pluck("name")->all())];
        }
        if(!empty($evidence->notes)) {
            $evidenceDetails[] = ["name" => "notes", "string" => $evidence->notes];
        }
        $evidenceHtml = "";
        foreach ($evidenceDetails as $evidenceDetail) {
            $evidenceName = \AdminLang::trans("disputes.evidence.label." . $evidenceDetail["name"]);
            $evidenceHtml .= "<p><strong>" . $evidenceName . "</strong><br>" . $evidenceDetail["string"] . "</p>";
        }
        return $evidenceHtml;
    }
    private function parseResponseEvidence($evidences) : array
    {
        $evidencesArray = [];
        foreach ($evidences as $evidence) {
            $evidencesArray[] = ["name" => strtolower($evidence->evidence_type), "value" => $this->generateEvidenceString($evidence)];
        }
        return $evidencesArray;
    }
    private function getDisputeEvidenceTypes($key) : array
    {
        $map = [self::MERCHANDISE_OR_SERVICE_NOT_RECEIVED => [self::PROOF_OF_FULFILLMENT, self::PROOF_OF_REFUND, self::OTHER], self::MERCHANDISE_OR_SERVICE_NOT_AS_DESCRIBED => [self::OTHER, self::PROOF_OF_REFUND], self::UNAUTHORISED => [self::PROOF_OF_FULFILLMENT, self::PROOF_OF_REFUND, self::OTHER], self::CREDIT_NOT_PROCESSED => [self::PROOF_OF_REFUND, self::OTHER], self::DUPLICATE_TRANSACTION => [self::PROOF_OF_FULFILLMENT, self::PROOF_OF_REFUND, self::OTHER], self::INCORRECT_AMOUNT => [self::PROOF_OF_REFUND, self::OTHER], self::PAYMENT_BY_OTHER_MEANS => [self::PROOF_OF_REFUND, self::OTHER], self::CANCELED_RECURRING_BILLING => [self::PROOF_OF_REFUND, self::OTHER], self::OTHER => [self::PROOF_OF_SHIPMENT, self::PROOF_OF_REFUND, self::OTHER]];
        return $map[$key];
    }
    private function getDisputeCustomData($evidenceKey)
    {
        $requiredString = \AdminLang::trans("global.required");
        $customDataDetails = ["notes" => ["name" => "notes", "type" => "textarea", "placeholder" => \AdminLang::trans("disputes.evidence.label.notes")], "document" => ["name" => "document", "type" => "file"], "carrierName" => ["name" => "carrier_name", "type" => "text", "placeholder" => \AdminLang::trans("disputes.evidence.label.carrierName") . " (" . $requiredString . ")"], "trackingNumber" => ["name" => "tracking_number", "type" => "text", "placeholder" => \AdminLang::trans("disputes.evidence.label.trackingNumber") . " (" . $requiredString . ")"], "refundIds" => ["name" => "refund_ids", "type" => "text", "placeholder" => \AdminLang::trans("disputes.evidence.label.refundId") . " (" . $requiredString . ")"]];
        switch ($evidenceKey) {
            case self::PROOF_OF_SHIPMENT:
            case self::PROOF_OF_FULFILLMENT:
                $customData = [$customDataDetails["trackingNumber"], $customDataDetails["carrierName"]];
                break;
            case self::PROOF_OF_REFUND:
                $customData = [$customDataDetails["refundIds"]];
                break;
            case self::OTHER:
                $customData = [$customDataDetails["document"], $customDataDetails["notes"]];
                break;
            default:
                $customData = "";
                return json_encode($customData);
        }
    }
    private function getTransientDataKey()
    {
        return $this->module->getLoadedModule() . "_DisputeData";
    }
}

?>