<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\Client;

class ProfileController
{
    public function consentHistory(\WHMCS\Http\Message\ServerRequest $request)
    {
        try {
            $client = \WHMCS\User\Client::findOrFail($request->get("client_id"));
            $body = view("admin.client.profile.consent-history", ["consentHistory" => $client->marketingConsent()->orderBy("created_at", "desc")]);
        } catch (\Exception $e) {
            $body = "An error occurred: " . $e->getMessage();
        }
        return new \WHMCS\Http\Message\JsonResponse(["body" => $body]);
    }
    public function profileContacts(\WHMCS\Http\Message\ServerRequest $request)
    {
        $userId = $request->getAttribute("userId");
        redir(["userid" => $userId], \WHMCS\Utility\Environment\WebHelper::getAdminBaseUrl() . "/clientscontacts.php");
    }
}

?>