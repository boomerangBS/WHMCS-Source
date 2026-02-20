<?php

namespace WHMCS\ApplicationLink\OpenID\Claim;

class Profile extends AbstractClaim
{
    public $name;
    public $family_name;
    public $given_name;
    public $preferred_username;
    public $locale;
    public $update_at;
    public function hydrate()
    {
        $user = $this->getUser();
        $this->name = $user->fullName;
        $this->family_name = $user->lastName;
        $this->given_name = $user->firstName;
        $this->preferred_username = $user->username;
        $this->update_at = $user->updatedAt->toDateTimeString();
        $lang = new \WHMCS\Language\ClientLanguage($user->language);
        $this->locale = str_replace("_", "-", $lang->getLanguageLocale());
        if(strpos($this->update_at, "0000") === 0 || strpos($this->update_at, "-0001") === 0) {
            $this->update_at = NULL;
        }
        return $this;
    }
}

?>