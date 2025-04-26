<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Payment;

class ClientSubscriptionServices
{
    private $client;
    private $associatedPaymentGateway;
    private $cancelled;
    private $serviceSubscriptions;
    private $addonSubscriptions;
    private $domainSubscriptions;
    public function __construct(\WHMCS\User\Client $client, array $associatedPaymentGateway = [])
    {
        $this->client = $client;
        $this->associatedPaymentGateway = $associatedPaymentGateway;
        $this->cancelled = new \Illuminate\Support\Collection([]);
        if(!function_exists("logTransaction")) {
            include_once sprintf("%s/includes/gatewayfunctions.php", ROOTDIR);
        }
    }
    public function all() : \Illuminate\Database\Eloquent\Collection
    {
        return $this->serviceSubscriptions()->concat($this->domainSubscriptions())->concat($this->addonSubscriptions());
    }
    public function cancelled() : \Illuminate\Support\Collection
    {
        return $this->cancelled;
    }
    private function commonModelSubscriptionFilter($limitedPaymentGateways) : \Closure
    {
        return function (\WHMCS\Service\SubscriptionAwareInterface $model) use($limitedPaymentGateways) {
            if(count($limitedPaymentGateways)) {
                return $model->getSubscriptionId() && in_array($model->getPaymentGatewayIdentifier(), $limitedPaymentGateways);
            }
            return $model->getSubscriptionId();
        };
    }
    public function addonSubscriptions() : \Illuminate\Database\Eloquent\Collection
    {
        if(is_null($this->addonSubscriptions)) {
            $this->addonSubscriptions = $this->clientSubsByEntity("addons");
        }
        return $this->addonSubscriptions;
    }
    public function serviceSubscriptions() : \Illuminate\Database\Eloquent\Collection
    {
        if(is_null($this->serviceSubscriptions)) {
            $this->serviceSubscriptions = $this->clientSubsByEntity("services");
        }
        return $this->serviceSubscriptions;
    }
    public function domainSubscriptions() : \Illuminate\Database\Eloquent\Collection
    {
        if(is_null($this->domainSubscriptions)) {
            $this->domainSubscriptions = $this->clientSubsByEntity("domains");
        }
        return $this->domainSubscriptions;
    }
    private function clientSubsByEntity($entityType) : \Illuminate\Database\Eloquent\Collection
    {
        return $this->client->{$entityType}->filter($this->commonModelSubscriptionFilter($this->associatedPaymentGateway));
    }
    public function allBySubscriptionId() : \Illuminate\Support\Collection
    {
        $allEntities = $this->all();
        $keyedBySubId = new \Illuminate\Support\Collection([]);
        $allEntities->each(function (\WHMCS\Service\SubscriptionAwareInterface $model) use($keyedBySubId) {
            $subId = $model->getSubscriptionId();
            if(empty($subId)) {
                return NULL;
            }
            $entitiesWithSameSubId = $keyedBySubId->get($subId);
            if(is_null($entitiesWithSameSubId)) {
                $entitiesWithSameSubId = new \Illuminate\Support\Collection([]);
            }
            $entitiesWithSameSubId->add($model);
            $keyedBySubId->put($subId, $entitiesWithSameSubId);
        });
        return $keyedBySubId->map(function (\Illuminate\Support\Collection $entities) {
            return $entities->sort(function ($a, $b) {
                if(is_null($a) || is_null($b)) {
                    return 0;
                }
                if($a instanceof \WHMCS\Service\Service) {
                    return -1;
                }
                if($b instanceof \WHMCS\Service\Service) {
                    return 1;
                }
                if($a instanceof \WHMCS\Domain\Domain) {
                    return -1;
                }
                if($b instanceof \WHMCS\Domain\Domain) {
                    return 1;
                }
                return 0;
            });
        });
    }
    public function cancel(\WHMCS\Service\SubscriptionAwareInterface $model) : array
    {
        $subscriptionId = $model->getSubscriptionId();
        $result = $model->cancelSubscription();
        if(is_null($result)) {
            return NULL;
        }
        $model->save();
        $this->logActivity($model, $result);
        $this->logTransaction($model, $result);
        if($result["status"] !== "success") {
            return NULL;
        }
        $this->addToCancelled($subscriptionId, $model);
        return $result;
    }
    private function attemptCancellation() : \Illuminate\Support\Collection
    {
        $notCancelled = new \Illuminate\Support\Collection([]);
        $subs = $this->allBySubscriptionId();
        $subs->each(function (\Illuminate\Support\Collection $entities, string $subscriptionId) use($notCancelled) {
            $previousAttempts = [];
            while (0 < count($entities)) {
                $model = $entities->shift();
                if(is_null($model)) {
                } elseif(in_array($model->getPaymentGatewayIdentifier(), $previousAttempts)) {
                    $notCancelled->push($model);
                } else {
                    $result = $this->cancel($model);
                    if(is_null($result)) {
                        $notCancelled->push($model);
                        $previousAttempts[] = $model->getPaymentGatewayIdentifier();
                    } else {
                        $entities->each(function (\WHMCS\Service\SubscriptionAwareInterface $otherModel) use($result, $subscriptionId) {
                            $this->logActivity($otherModel, $result);
                            $otherModel->removeSubscriptionId();
                            $otherModel->save();
                            $this->addToCancelled($subscriptionId, $otherModel);
                        });
                        $entities = new \Illuminate\Support\Collection([]);
                    }
                }
            }
            unset($previousAttempts);
        });
        return $notCancelled;
    }
    private function resetInternalStores()
    {
        $this->serviceSubscriptions = NULL;
        $this->domainSubscriptions = NULL;
        $this->addonSubscriptions = NULL;
    }
    private function pushInStores(\WHMCS\Service\SubscriptionAwareInterface $model)
    {
        if($model instanceof \WHMCS\Service\Service) {
            if(is_null($this->serviceSubscriptions)) {
                $this->serviceSubscriptions = new \Illuminate\Database\Eloquent\Collection([]);
            }
            $this->serviceSubscriptions->put($model->id, $model);
        } elseif($model instanceof \WHMCS\Domain\Domain) {
            if(is_null($this->domainSubscriptions)) {
                $this->domainSubscriptions = new \Illuminate\Database\Eloquent\Collection([]);
            }
            $this->domainSubscriptions->put($model->id, $model);
        } elseif($model instanceof \WHMCS\Service\Addon) {
            if(is_null($this->addonSubscriptions)) {
                $this->addonSubscriptions = new \Illuminate\Database\Eloquent\Collection([]);
            }
            $this->addonSubscriptions->put($model->id, $model);
        }
    }
    private function pushInStoresIfNotCancelled(\Illuminate\Support\Collection $notCancelledWithSetGateway)
    {
        $notCancelledWithSetGateway->each(function (\WHMCS\Service\SubscriptionAwareInterface $model) {
            $subscriptionId = $model->getSubscriptionId();
            if(!$this->cancelled->has($subscriptionId)) {
                $this->pushInStores($model);
            } else {
                $this->logActivity($model, ["status" => "success"]);
                $model->removeSubscriptionId();
                $model->save();
                $this->addToCancelled($subscriptionId, $model);
            }
        });
    }
    public function cancelAllSubscriptions() : void
    {
        $notCancelled = $this->attemptCancellation();
        $this->resetInternalStores();
        $this->pushInStoresIfNotCancelled($notCancelled);
    }
    private function addToCancelled($subscriptionIdentifier, $model) : void
    {
        if($model->getSubscriptionId() != "") {
            throw new \LogicException("subscription not yet cancelled");
        }
        $entitiesWithSameSubId = $this->cancelled->get($subscriptionIdentifier);
        if(is_null($entitiesWithSameSubId)) {
            $entitiesWithSameSubId = new \Illuminate\Support\Collection([]);
        }
        $entitiesWithSameSubId->add($model);
        $this->cancelled->put($subscriptionIdentifier, $entitiesWithSameSubId);
    }
    private function logActivity(\WHMCS\Service\SubscriptionAwareInterface $model, array $result)
    {
        $subId = $model->getSubscriptionId();
        if($result["status"] == "success") {
            $msg = "Subscription Cancellation for ID " . $subId . " Successful";
        } else {
            $msg = "Subscription Cancellation for ID " . $subId . " Failed";
            if(!empty($result["errorMsg"])) {
                $msg .= ": " . $result["errorMsg"];
            }
        }
        $suffix = "";
        if($model instanceof \WHMCS\Service\Addon) {
            $suffix = " - Service Addon ID: " . $model->id;
        } elseif($model instanceof \WHMCS\Domain\Domain) {
            $suffix = " - Domain ID: " . $model->id;
        } elseif($model instanceof \WHMCS\Service\Service) {
            $suffix = " - Service ID: " . $model->id;
        }
        logActivity($msg . $suffix, $model->clientId);
    }
    private function logTransaction($model, $result) : void
    {
        if($result["status"] == "success") {
            $msg = "Subscription Cancellation Success";
        } else {
            $msg = "Subscription Cancellation Failed";
        }
        $details = $result["rawdata"] ?? "Unexpected module response";
        logTransaction($model->getPaymentGatewayIdentifier(), $details, $msg);
    }
}

?>