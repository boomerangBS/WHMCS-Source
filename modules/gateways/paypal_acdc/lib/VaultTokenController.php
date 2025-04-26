<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Module\Gateway\paypal_acdc;

class VaultTokenController extends \WHMCS\Module\Gateway\paypal_ppcpv\VaultTokenController
{
    public static function factory(\WHMCS\Module\Gateway $module)
    {
        if(Core::MODULE_NAME !== $module->getLoadedModule()) {
            throw new \Exception("Mismatched gateway module");
        }
        return new static($module);
    }
    public function vaultedTokenFromApiResponse(\WHMCS\Module\Gateway\paypal_ppcpv\API\AbstractResponse $response) : \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken
    {
        return $this->parseResponse($response);
    }
    public function tokenFromPayMethod(\WHMCS\Payment\PayMethod\Model $payMethod) : \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken
    {
        $paymentSource = (object) ["card" => (object) ["last_digits" => $payMethod->payment->last_four, "brand" => $payMethod->payment->card_type, "expiry" => $payMethod->payment->expiry_date->format("Y-m")]];
        $token = \WHMCS\Module\Gateway\paypal_ppcpv\Util::decodeJSON($payMethod->payment->getRemoteToken());
        if($token === false) {
            return NULL;
        }
        return \WHMCS\Module\Gateway\paypal_ppcpv\API\Entity\VaultedToken::factory($token->customerId, $token->vaultId, $token->transactionIdentifier, $paymentSource);
    }
    public function saveVaultedToken(\WHMCS\User\Client $client, $vaultedToken = NULL, $billingContact = NULL, string $customDescription) : \WHMCS\Payment\Contracts\PayMethodInterface
    {
        $newToken = $vaultedToken->transformToTokenJSON();
        $payMethod = $this->existingPayMethodByToken($client, $vaultedToken);
        if(!is_null($payMethod)) {
            return $payMethod;
        }
        $payMethod = $this->newPayMethod($client, $customDescription ?? "", $billingContact);
        $payMethod->payment->setCardType($vaultedToken->brand());
        $payMethod->payment->setLastFour($vaultedToken->cardHint());
        if($vaultedToken->cardExpiry()) {
            $payMethod->payment->setExpiryDate(\WHMCS\Carbon::parse($vaultedToken->cardExpiry())->endOfMonth());
        }
        $payMethod->payment->setRemoteToken($newToken);
        $payMethod->payment->save();
        return $payMethod;
    }
}

?>