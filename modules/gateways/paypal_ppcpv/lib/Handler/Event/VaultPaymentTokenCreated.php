<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv\Handler\Event;
class VaultPaymentTokenCreated extends AbstractWebhookHandler
{
    use WebhookAPIControllerRequired;
    use VaultTokenControllerRequired;
    public function handle(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, &$outcomes) : \WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent
    {
        if(is_null($event->resourceMetaData())) {
            return "Vault Payment Token Created";
        }
        $orderIdentifier = $event->orderIdentifier();
        if(strlen($orderIdentifier) == 0) {
            return "Vault Payment Token Created - Unknown";
        }
        $moduleName = $event->initiatingModule();
        $invoiceId = $this->getInvoiceIdentifier($orderIdentifier);
        $transactionHistory = $this->searchTransactionHistoryById($moduleName, $invoiceId, $event->orderIdentifier());
        if(is_null($transactionHistory)) {
            throw new \Exception("Order " . $orderIdentifier . " not found in invoice " . $invoiceId . " transaction history");
        }
        $vaultedToken = $this->vaultedToken($event, $transactionHistory->transactionId);
        if(is_null($vaultedToken)) {
            throw new \Exception("Prior transaction ID not found for order " . $orderIdentifier);
        }
        $invoice = \WHMCS\Billing\Invoice::findOrFail($invoiceId);
        $billingContact = NULL;
        if($vaultedToken instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedTokenCard) {
            $billingContact = $this->getBillingContact($invoice->client, $event);
        }
        $this->vaultTokenController->saveVaultedToken($invoice->client, $vaultedToken, $billingContact);
        return "Vault Payment Token Created";
    }
    protected function getInvoiceIdentifier($orderIdentifier)
    {
        $order = $this->getOrderDetail($orderIdentifier);
        return $order->invoiceIdentifier();
    }
    protected function getOrderDetail($orderIdentifier) : \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusResponse
    {
        $order = $this->api->send((new \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusRequest($this->api))->setOrderIdentifier($orderIdentifier));
        if(!$order instanceof \WHMCS\Module\Gateway\paypal_ppcpv\API\OrderStatusResponse) {
            throw new \Exception("Failed to retrieve details for order # " . $orderIdentifier);
        }
        return $order;
    }
    public function searchTransactionHistoryById($gateway, string $invoiceId, string $orderIdentifier) : \WHMCS\Billing\Payment\Transaction\History
    {
        $len = strlen($gateway);
        $histories = \WHMCS\Billing\Payment\Transaction\History::where("invoice_id", $invoiceId)->whereRaw("LEFT(`additional_information`, " . $len . " + 2) = '" . $gateway . "|{'" . " OR LEFT(`additional_information`, " . $len . " + 3) = '\"" . $gateway . "|{'")->get();
        foreach ($histories as $history) {
            $data = \WHMCS\Module\Gateway\paypal_ppcpv\Logger::historyUnpackAdditional($history->additionalInformation);
            if(is_object($data)) {
                $packedOrderResponse = $data->packedOrderResponse;
            } else {
                $packedOrderResponse = $data;
            }
            $order = (new \WHMCS\Module\Gateway\paypal_ppcpv\API\CapturePaymentResponse())->unpackOrderResponse($packedOrderResponse);
            if($order->id === $orderIdentifier) {
                return $history;
            }
        }
        return NULL;
    }
    public function vaultedToken(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractWebhookEvent $event, string $transactionIdentifier) : \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken
    {
        return \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken::factory($event->customer->id, $event->id, $transactionIdentifier, $event->payment_source);
    }
    private function getBillingContact(\WHMCS\User\Client $client, $event) : \WHMCS\User\Client\Contact
    {
        $contact = $event->billingContactFromCard($event->paymentSourceCard());
        if($this->contactsEqual($client, $contact)) {
            return NULL;
        }
        return $this->findOrCreateBillingContact($client, $contact);
    }
    private function findOrCreateBillingContact(\WHMCS\User\Client $client, $contact) : \WHMCS\User\Client\Contact
    {
        $existingContact = $this->findContact($client, $contact);
        if(is_null($existingContact)) {
            $contact->clientId = $client->id;
            $contact->save();
        } else {
            $contact = $existingContact;
        }
        return $contact;
    }
    private function findContact(\WHMCS\User\Client $client, $newContact) : \WHMCS\User\Client\Contact
    {
        foreach ($client->contacts as $contact) {
            if($this->contactsEqual($contact, $newContact)) {
                return $contact;
            }
        }
        return NULL;
    }
    private function contactsEqual(\WHMCS\User\Contracts\ContactInterface $contactA, $contactB) : \WHMCS\User\Contracts\ContactInterface
    {
        return strcasecmp($contactA->fullName, $contactB->fullName) == 0 && strcasecmp($contactA->address1, $contactB->address1) == 0 && strcasecmp($contactA->address2, $contactB->address2) == 0 && strcasecmp($contactA->city, $contactB->city) == 0 && strcasecmp($contactA->state, $contactB->state) == 0 && strcasecmp($contactA->postcode, $contactB->postcode) == 0 && strcasecmp($contactA->country, $contactB->country) == 0;
    }
}

?>