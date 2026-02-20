<?php

namespace WHMCS\Billing\Payment;

class Dispute implements \Illuminate\Contracts\Support\Arrayable, DisputeInterface
{
    use \WHMCS\Module\Gateway\CurrencyObjectTrait;
    protected $id = "";
    protected $amount = 0;
    protected $currencyCode = "";
    protected $transactionId = "";
    protected $createdDate;
    protected $respondByDate;
    protected $reason = "";
    protected $status = "";
    protected $gateway = "";
    protected $evidence = [];
    protected $evidenceType = [];
    protected $visibleTypes = [];
    protected $customData = [];
    protected $isUpdatable = false;
    protected $isClosable = false;
    protected $isSubmittable = false;
    protected $manageHref = "";
    protected function setId($id) : \self
    {
        $this->id = $id;
        return $this;
    }
    protected function setAmount($amount) : \self
    {
        $this->amount = $amount;
        return $this;
    }
    protected function setCurrencyCode($currencyCode) : \self
    {
        $this->currencyCode = $currencyCode;
        return $this;
    }
    public function setTransactionId($transactionId) : DisputeInterface
    {
        $this->transactionId = $transactionId;
        return $this;
    }
    protected function setCreatedDate(\WHMCS\Carbon $createdDate) : \self
    {
        $this->createdDate = $createdDate;
        return $this;
    }
    protected function setRespondByDate(\WHMCS\Carbon $respondByDate) : \self
    {
        $this->respondByDate = $respondByDate;
        return $this;
    }
    protected function setReason($reason) : \self
    {
        $this->reason = $reason;
        return $this;
    }
    protected function setStatus($status) : \self
    {
        $this->status = $status;
        return $this;
    }
    public static function factory($id, $amount, string $currencyCode, string $transactionId, $createdDate, $respondBy, string $reason, string $status) : DisputeInterface
    {
        $self = new static();
        $self->setId($id)->setAmount($amount)->setCurrencyCode($currencyCode)->setTransactionId($transactionId)->setCreatedDate($createdDate)->setRespondByDate($respondBy)->setReason($reason)->setStatus($status);
        return $self;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getCreatedDate() : \WHMCS\Carbon
    {
        return $this->createdDate;
    }
    public function getRespondByDate() : \WHMCS\Carbon
    {
        return $this->respondByDate;
    }
    public function getAmount() : \WHMCS\View\Formatter\Price
    {
        return new \WHMCS\View\Formatter\Price($this->amount, $this->getCurrencyObject());
    }
    public function getCurrencyCode()
    {
        return strtoupper($this->currencyCode);
    }
    public function getTransactionId()
    {
        $transaction = Transaction::where("transid", $this->transactionId)->first();
        if($transaction) {
            return $transaction->getTransactionIdMarkup();
        }
        return $this->transactionId;
    }
    public function getReason()
    {
        if(!$this->reason) {
            return \AdminLang::trans("global.unknown");
        }
        return \AdminLang::trans("disputes.reasons." . $this->reason);
    }
    public function getStatus()
    {
        if(!$this->status) {
            return \AdminLang::trans("global.unknown");
        }
        return \AdminLang::trans("disputes.statuses." . $this->status);
    }
    public function setGateway($gateway) : DisputeInterface
    {
        $this->gateway = $gateway;
        return $this;
    }
    public function getGateway()
    {
        return $this->gateway;
    }
    public function setEvidence($evidence) : DisputeInterface
    {
        $this->evidence = array_merge($this->evidence, $evidence);
        return $this;
    }
    public function getEvidence() : array
    {
        return $this->evidence;
    }
    protected function setEvidenceTypes($evidenceTypes) : \self
    {
        $this->evidenceType = $evidenceTypes;
        return $this;
    }
    public function setEvidenceType($evidenceKey, string $evidenceType) : DisputeInterface
    {
        $this->evidenceType[$evidenceKey] = $evidenceType;
        return $this;
    }
    public function getEvidenceType($evidenceKey)
    {
        if(!empty($this->evidenceType[$evidenceKey])) {
            return $this->evidenceType[$evidenceKey];
        }
        return "text";
    }
    public function getEvidenceTypes() : array
    {
        return $this->evidenceType;
    }
    public function setVisibleTypes($evidenceKey) : \self
    {
        $this->visibleTypes = $evidenceKey;
        return $this;
    }
    public function getVisibleTypes()
    {
        return !empty($this->visibleTypes) ? $this->visibleTypes : [];
    }
    public function setCustomData($evidenceKey, string $customData) : \self
    {
        $this->customData[strtolower($evidenceKey)] = $customData;
        return $this;
    }
    public function getCustomData(string $evidenceKey, $decode = false)
    {
        $customData = !empty($this->customData[$evidenceKey]) ? $this->customData[$evidenceKey] : "";
        return $decode ? json_decode($customData) : $customData;
    }
    public function setIsUpdatable($updatable) : DisputeInterface
    {
        $this->isUpdatable = $updatable;
        return $this;
    }
    public function getIsUpdatable()
    {
        return $this->isUpdatable;
    }
    public function setIsSubmittable($submittable) : DisputeInterface
    {
        $this->isSubmittable = $submittable;
        return $this;
    }
    public function getIsSubmittable()
    {
        return $this->isSubmittable;
    }
    public function setIsClosable($closable) : DisputeInterface
    {
        $this->isClosable = $closable;
        return $this;
    }
    public function getIsClosable()
    {
        return $this->isClosable;
    }
    public function getManageHref()
    {
        return $this->manageHref;
    }
    public function setManageHref($href) : \self
    {
        $this->manageHref = $href;
        return $this;
    }
    public function getViewHref()
    {
        return routePath("admin-billing-disputes-view", $this->getGateway(), $this->getId());
    }
    public function getCloseHref()
    {
        return routePath("admin-billing-disputes-close", $this->getGateway(), $this->getId());
    }
    public function getSubmitHref()
    {
        return routePath("admin-billing-disputes-submit", $this->getGateway(), $this->getId());
    }
    public function getUpdateHref()
    {
        return routePath("admin-billing-disputes-evidence-submit", $this->getGateway(), $this->getId());
    }
    public function toArray() : array
    {
        return ["id" => $this->getId(), "amount" => $this->getAmount(), "currencyCode" => $this->getCurrencyCode(), "transactionId" => $this->getTransactionId(), "createdDate" => $this->getCreatedDate(), "respondBy" => $this->getRespondByDate(), "reason" => $this->getReason(), "status" => $this->getStatus(), "gateway" => $this->getGateway(), "evidence" => $this->getEvidence(), "evidenceTypes" => $this->getEvidenceTypes(), "isUpdatable" => $this->getIsUpdatable()];
    }
    public static function factoryFromArray($dispute) : Dispute
    {
        $new = static::factory($dispute["id"], $dispute["amount"], $dispute["currencyCode"], $dispute["transactionId"], $dispute["createdDate"], $dispute["respondBy"], $dispute["reason"], $dispute["status"]);
        $optionalParams = ["gateway", "evidence", "evidenceTypes", "isUpdatable"];
        foreach ($optionalParams as $optionalParam) {
            if(array_key_exists($optionalParam, $dispute)) {
                $method = "set" . ucfirst($optionalParam);
                $new->{$method}($dispute[$optionalParam]);
            }
        }
        return $new;
    }
}

?>