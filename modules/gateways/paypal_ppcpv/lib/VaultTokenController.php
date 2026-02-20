<?php


namespace WHMCS\Module\Gateway\paypal_ppcpv;
class VaultTokenController
{
    protected $module;
    public static function factoryModule(\WHMCS\Module\Gateway $module) : \self
    {
        if(\WHMCS\Module\Gateway\paypal_acdc\Core::MODULE_NAME === $module->getLoadedModule()) {
            return \WHMCS\Module\Gateway\paypal_acdc\VaultTokenController::factory($module);
        }
        if(PayPalCommerce::MODULE_NAME === $module->getLoadedModule()) {
            return static::factory($module);
        }
        throw new \Exception("Gateway Module is not supported");
    }
    public static function factory(\WHMCS\Module\Gateway $module)
    {
        if(PayPalCommerce::MODULE_NAME !== $module->getLoadedModule()) {
            throw new \Exception("Mismatched gateway module");
        }
        return new static($module);
    }
    public function __construct(\WHMCS\Module\Gateway $module)
    {
        $this->module = $module;
        return $this;
    }
    public function vaultedTokenFromCapturePayment(API\CapturePaymentResponse $response) : API\Entity\VaultedToken
    {
        return $this->parseResponse($response);
    }
    public function saveVaultedToken(\WHMCS\User\Client $client, $vaultedToken) : \WHMCS\Payment\Contracts\PayMethodInterface
    {
        $newToken = $vaultedToken->transformToTokenJSON();
        $payMethod = $this->existingPayMethodByToken($client, $vaultedToken);
        if(!is_null($payMethod)) {
            return $payMethod;
        }
        $payMethod = $this->newPayMethod($client, "");
        $payMethod->payment->setCardType($vaultedToken->brand());
        $payMethod->payment->setRemoteToken($newToken);
        $payMethod->payment->setLastFour($vaultedToken->payPalEmail());
        $payMethod->payment->setExpiryDate($vaultedToken->cardExpiry());
        $payMethod->payment->save();
        return $payMethod;
    }
    public function deleteVaultedToken($vaultedId) : void
    {
        \WHMCS\Payment\PayMethod\Model::gatewayName($this->module->getLoadedModule())->chunk(100, function ($payMethods) {
            static $vaultedId = NULL;
            foreach ($payMethods as $payMethod) {
                $existingToken = $payMethod->payment->getRemoteToken();
                if(strlen($existingToken) == 0) {
                } else {
                    $token = Util::decodeJSON($existingToken);
                    if($token !== false && $token->vaultId === $vaultedId) {
                        $payMethod->delete();
                    }
                }
            }
        });
    }
    protected function parsePaymentSource($paymentSource) : API\Entity\VaultedToken
    {
        return API\Entity\VaultedToken::factoryByPaymentSource($paymentSource);
    }
    protected function parseResponse($response) : API\Entity\VaultedToken
    {
        $basicVaultToken = $this->parsePaymentSource($response->payment_source);
        if(is_null($basicVaultToken)) {
            return NULL;
        }
        return API\Entity\VaultedToken::factory($basicVaultToken->customerId(), $basicVaultToken->vaultId(), $response->transactionIdentifier(), $response->payment_source);
    }
    protected function newPayMethod(\WHMCS\User\Client $client, string $payMethodDescription = NULL, $billingContact) : \WHMCS\Payment\PayMethod\Model
    {
        $payMethod = \WHMCS\Payment\PayMethod\Adapter\RemoteCreditCard::factoryPayMethod($client, $billingContact, $payMethodDescription);
        $payMethod->setGateway($this->module)->save();
        return $payMethod;
    }
    public function existingVaultToken(\WHMCS\User\Client $client) : API\Entity\VaultedToken
    {
        return $this->existingVaultTokens($client)->first();
    }
    public function existingVaultTokens(\WHMCS\User\Client $client) : \Illuminate\Support\Collection
    {
        $vaults = collect();
        $payMethods = $this->existingPayMethods($client);
        foreach ($payMethods as $payMethod) {
            $vaults->push($this->tokenFromPayMethod($payMethod));
        }
        return $vaults;
    }
    public function tokenFromPayMethod(\WHMCS\Payment\PayMethod\Model $payMethod) : API\Entity\VaultedToken
    {
        $paymentSource = (object) ["paypal" => (object) ["email_address" => $payMethod->description]];
        $token = Util::decodeJSON($payMethod->payment->getRemoteToken());
        if($token === false) {
            return NULL;
        }
        return API\Entity\VaultedToken::factory($token->customerId, $token->vaultId, $token->transactionIdentifier, $paymentSource);
    }
    protected function existingPayMethods(\WHMCS\User\Client $client) : \Illuminate\Database\Eloquent\Collection
    {
        return $client->payMethods()->where("gateway_name", $this->module->getLoadedModule())->get();
    }
    protected function existingPayMethodByToken(\WHMCS\User\Client $client, $token) : \WHMCS\Payment\PayMethod\Model
    {
        $payMethods = $this->existingPayMethods($client);
        foreach ($payMethods as $payMethod) {
            $existingToken = $this->tokenFromPayMethod($payMethod);
            if(!is_null($existingToken) && $existingToken->equals($token)) {
                return $payMethod;
            }
        }
        return NULL;
    }
}

?>