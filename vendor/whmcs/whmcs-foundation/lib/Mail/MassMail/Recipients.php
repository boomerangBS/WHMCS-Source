<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Mail\MassMail;

class Recipients
{
    protected $configuration = [];
    protected $offset = 0;
    protected $limit = 0;
    protected $totalRecipients = 0;
    protected $recipients = [];
    const DEFAULT_LIMIT = 50;
    public static function factory($configuration = 0, int $offset = self::DEFAULT_LIMIT, int $limit) : Recipients
    {
        $mail = new self();
        $mailType = strtolower($configuration["email_type"]);
        if(!in_array($mailType, ["general", "addon", "affiliate", "product", "domain"])) {
            $mailType = "general";
        }
        $mail->setConfiguration($configuration)->setOffset($offset)->setLimit($limit)->{$mailType}();
        return $mail;
    }
    public static function getRecipientLimit() : int
    {
        return self::DEFAULT_LIMIT;
    }
    protected function addRecipient($recipient) : void
    {
        $this->recipients[] = $recipient;
    }
    public function getRecipients() : array
    {
        return $this->recipients;
    }
    protected function setOffset($offset) : \self
    {
        $this->offset = (int) $offset;
        return $this;
    }
    protected function getOffset() : int
    {
        return $this->offset;
    }
    protected function setLimit($limit) : \self
    {
        $this->limit = $limit;
        return $this;
    }
    protected function getLimit() : int
    {
        return $this->limit;
    }
    protected function setConfiguration($configuration) : \self
    {
        $this->configuration = $configuration;
        return $this;
    }
    protected function getConfiguration() : array
    {
        return $this->configuration;
    }
    protected function setTotalRecipients($totalRecipients) : \self
    {
        $this->totalRecipients = $totalRecipients;
        return $this;
    }
    public function getTotalRecipients() : int
    {
        return $this->totalRecipients;
    }
    protected function addon() : void
    {
        $configuration = $this->getConfiguration();
        $recipients = \WHMCS\Service\Addon::where("id", "!=", "");
        if(!empty($configuration["selected_ids"]) && is_array($configuration["selected_ids"])) {
            $recipients->whereIn("id", $configuration["selected_ids"]);
            if(!empty($configuration["email_opt_out"])) {
                if(\WHMCS\Marketing\EmailSubscription::isUsingOptInField()) {
                    $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                        $query->where("marketing_emails_opt_in", 1);
                    });
                } else {
                    $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                        $query->where("emailoptout", 0);
                    });
                }
            }
            $recipientCollection = $recipients->get();
            $this->setTotalRecipients($recipientCollection->count());
        } else {
            if(!empty($configuration["addon_ids"])) {
                $recipients->whereIn("addonid", $configuration["addon_ids"]);
            }
            if(!empty($configuration["addon_statuses"])) {
                $recipients->whereIn("status", $configuration["addon_statuses"]);
            }
            $this->clientFilters($recipients);
            $countQuery = clone $recipients;
            if(empty($configuration["send_for_each"])) {
                $countQuery->distinct("userid");
            }
            $this->setTotalRecipients($countQuery->count());
            $this->applyLimitAndOffset($recipients);
            if(empty($configuration["send_for_each"])) {
                $recipients->groupBy("userid");
            }
            $recipientCollection = $recipients->get();
        }
        foreach ($recipientCollection as $recipient) {
            $this->addRecipient($recipient);
        }
    }
    protected function affiliate() : void
    {
        $configuration = $this->getConfiguration();
        $recipients = \WHMCS\User\Client\Affiliate::where("id", "!=", "");
        if(!empty($configuration["selected_ids"]) && is_array($configuration["selected_ids"])) {
            $recipients->whereIn("id", $configuration["selected_ids"]);
        }
        if(!empty($configuration["email_opt_out"])) {
            if(\WHMCS\Marketing\EmailSubscription::isUsingOptInField()) {
                $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where("marketing_emails_opt_in", 1);
                });
            } else {
                $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                    $query->where("emailoptout", 0);
                });
            }
        }
        $this->setTotalRecipients($recipients->count());
        foreach ($recipients->get() as $recipient) {
            $this->addRecipient($recipient);
        }
    }
    protected function domain() : void
    {
        $configuration = $this->getConfiguration();
        $recipients = \WHMCS\Domain\Domain::where("id", "!=", "");
        if(!empty($configuration["selected_ids"]) && is_array($configuration["selected_ids"])) {
            $recipients->whereIn("id", $configuration["selected_ids"]);
            if(!empty($configuration["email_opt_out"])) {
                if(\WHMCS\Marketing\EmailSubscription::isUsingOptInField()) {
                    $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                        $query->where("marketing_emails_opt_in", 1);
                    });
                } else {
                    $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                        $query->where("emailoptout", 0);
                    });
                }
            }
            $recipientCollection = $recipients->get();
            $this->setTotalRecipients($recipientCollection->count());
        } else {
            if(!empty($configuration["domain_statuses"])) {
                $recipients->whereIn("status", $configuration["domain_statuses"]);
            }
            $this->clientFilters($recipients);
            $countQuery = clone $recipients;
            if(empty($configuration["send_for_each"])) {
                $countQuery->distinct("userid");
            }
            $this->setTotalRecipients($countQuery->count());
            $this->applyLimitAndOffset($recipients);
            if(empty($configuration["send_for_each"])) {
                $recipients->groupBy("userid");
            }
            $recipientCollection = $recipients->get();
        }
        foreach ($recipientCollection as $recipient) {
            $this->addRecipient($recipient);
        }
    }
    protected function general() : void
    {
        $configuration = $this->getConfiguration();
        $recipients = \WHMCS\User\Client::where("id", "!=", "");
        if(!empty($configuration["selected_ids"]) && is_array($configuration["selected_ids"])) {
            $recipients->whereIn("id", $configuration["selected_ids"]);
            if(!empty($configuration["email_opt_out"])) {
                if(\WHMCS\Marketing\EmailSubscription::isUsingOptInField()) {
                    $recipients->where("marketing_emails_opt_in", 1);
                } else {
                    $recipients->where("emailoptout", 0);
                }
            }
            $this->setTotalRecipients($recipients->count());
        } else {
            if(!empty($configuration["email_opt_out"])) {
                if(\WHMCS\Marketing\EmailSubscription::isUsingOptInField()) {
                    $recipients->where("marketing_emails_opt_in", 1);
                } else {
                    $recipients->where("emailoptout", 0);
                }
            }
            if(!empty($configuration["client_status"])) {
                $recipients->whereIn("status", $configuration["client_status"]);
            }
            if(!empty($configuration["client_group"])) {
                $recipients->whereIn("groupid", $configuration["client_group"]);
            }
            if(!empty($configuration["client_country"])) {
                $recipients->whereIn("country", $configuration["client_country"]);
            }
            if(!empty($configuration["client_language"])) {
                $recipients->whereIn("language", $configuration["client_language"]);
            }
            if(!empty($configuration["custom_fields"])) {
                $customFields = array_filter($configuration["custom_fields"]);
                $whereHasValues = $whereDoesntHaveValues = [];
                foreach ($customFields as $fieldId => $value) {
                    if($value == "cfon") {
                        $whereHasValues[$fieldId] = "on";
                    } elseif($value == "cfoff") {
                        $whereDoesntHaveValues[] = $fieldId;
                    } else {
                        $whereHasValues[$fieldId] = $value;
                    }
                }
                if(!empty($whereHasValues)) {
                    $recipients->whereHas("customFieldValues", function (\Illuminate\Database\Eloquent\Builder $query) {
                        static $whereHasValues = NULL;
                        foreach ($whereHasValues as $fieldId => $value) {
                            $query->where("fieldid", $fieldId)->where("value", $value);
                        }
                    });
                }
                if(!empty($whereDoesntHaveValues)) {
                    $recipients->whereDoesntHave("customFieldValues", function (\Illuminate\Database\Eloquent\Builder $query) {
                        static $whereDoesntHaveValues = NULL;
                        foreach ($whereDoesntHaveValues as $fieldId) {
                            $query->where("fieldid", $fieldId)->where("value", "!=", "");
                        }
                    });
                }
            }
            $this->setTotalRecipients($recipients->count());
            $this->applyLimitAndOffset($recipients);
        }
        foreach ($recipients->get() as $recipient) {
            $this->addRecipient($recipient);
        }
    }
    protected function product() : void
    {
        $configuration = $this->getConfiguration();
        $recipients = \WHMCS\Service\Service::where("id", "!=", "");
        if(!empty($configuration["selected_ids"]) && is_array($configuration["selected_ids"])) {
            $recipients->whereIn("id", $configuration["selected_ids"]);
            if(!empty($configuration["email_opt_out"])) {
                if(\WHMCS\Marketing\EmailSubscription::isUsingOptInField()) {
                    $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                        $query->where("marketing_emails_opt_in", 1);
                    });
                } else {
                    $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                        $query->where("emailoptout", 0);
                    });
                }
            }
            $recipientCollection = $recipients->get();
            $this->setTotalRecipients($recipientCollection->count());
        } else {
            if(!empty($configuration["package_ids"])) {
                $recipients->whereIn("packageid", $configuration["package_ids"]);
            }
            if(!empty($configuration["product_statuses"])) {
                $recipients->whereIn("domainstatus", $configuration["product_statuses"]);
            }
            if(!empty($configuration["servers"])) {
                $recipients->whereIn("server", $configuration["servers"]);
            }
            $this->clientFilters($recipients);
            $countQuery = clone $recipients;
            if(empty($configuration["send_for_each"])) {
                $countQuery->distinct("userid");
            }
            $this->setTotalRecipients($countQuery->count());
            $this->applyLimitAndOffset($recipients);
            if(empty($configuration["send_for_each"])) {
                $recipients->groupBy("userid");
            }
            $recipientCollection = $recipients->get();
        }
        foreach ($recipientCollection as $recipient) {
            $this->addRecipient($recipient);
        }
    }
    protected function clientFilters(\Illuminate\Database\Eloquent\Builder $recipients) : void
    {
        $configuration = $this->getConfiguration();
        $filters = [];
        if(!empty($configuration["email_opt_out"])) {
            if(\WHMCS\Marketing\EmailSubscription::isUsingOptInField()) {
                $filters["marketing_emails_opt_in"] = 1;
            } else {
                $filters["emailoptout"] = 0;
            }
        }
        if(!empty($configuration["client_status"])) {
            $filters["status"] = $configuration["client_status"];
        }
        if(!empty($configuration["client_group"])) {
            $filters["groupid"] = $configuration["client_group"];
        }
        if(!empty($configuration["client_country"])) {
            $filters["country"] = $configuration["client_country"];
        }
        if(!empty($configuration["client_language"])) {
            $filters["language"] = $configuration["client_language"];
        }
        if(!empty($configuration["custom_fields"])) {
            $customFields = array_filter($configuration["custom_fields"]);
            if($customFields) {
                $filters["custom_fields"] = $customFields;
            }
        }
        if(0 < count($filters)) {
            $recipients->whereHas("client", function (\Illuminate\Database\Eloquent\Builder $query) {
                static $filters = NULL;
                foreach ($filters as $field => $value) {
                    if(!is_array($value)) {
                        $query->where($field, $value);
                    } else {
                        switch ($field) {
                            case "custom_fields":
                                $customFields = $value;
                                if($customFields) {
                                    $query->whereHas("customFieldValues", function (\Illuminate\Database\Eloquent\Builder $query) {
                                        static $customFields = NULL;
                                        foreach ($customFields as $fieldId => $value) {
                                            if($value == "cfon") {
                                                $value = "on";
                                            }
                                            if($value == "cfoff") {
                                                $query->where("fieldid", $fieldId)->where(function (\Illuminate\Database\Eloquent\Builder $query2) {
                                                    $query2->where("value", "")->orWhereNull("value");
                                                });
                                            } else {
                                                $query->where("fieldid", $fieldId)->where("value", $value);
                                            }
                                        }
                                    });
                                }
                                break;
                            default:
                                $query->whereIn($field, $value);
                        }
                    }
                }
            });
        }
    }
    protected function applyLimitAndOffset(\Illuminate\Database\Eloquent\Builder $recipients) : void
    {
        if(0 < $this->getLimit()) {
            $recipients->limit($this->getLimit())->offset($this->getOffset());
        }
    }
}

?>