<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\Admin\AdminInvites\Forms;

class AdminInviteFormType
{
    protected $formFields = ["email", "username", "firstname", "lastname", "roleid", "roleid", "deptids", "ticketnotify", "signature", "notes", "template", "language", "disabled", "credentialConfiguration"];
    protected $formData = [];
    private $validator;
    private $fetched = false;
    public function __construct(\WHMCS\Validate $validator)
    {
        if(is_null($validator)) {
            $validator = new \WHMCS\Validate();
        }
        $this->validator = $validator;
    }
    public function fetchRequestData() : void
    {
        foreach ($this->formFields as $field) {
            $this->formData[$field] = \App::getFromRequest($field, NULL);
        }
        $this->fetched = true;
    }
    public function getFormFieldValue(string $key)
    {
        if(!$this->fetched) {
            $this->fetchRequestData();
        }
        if(!isset($this->formData[$key])) {
            return NULL;
        }
        return $this->formData[$key];
    }
    public function validate()
    {
        $this->validateEmail();
        $this->validateUsername();
        $this->validateFirstname();
        return $this->validator->hasErrors() === 0;
    }
    private function validateFirstname() : void
    {
        $this->validator->validate("required", "firstname", ["administrators", "namerequired"]);
    }
    private function validateEmail() : void
    {
        $email = $this->getFormFieldValue("email");
        $email = trim($email);
        if($this->validator->validate("required", "email", ["administrators", "emailerror"]) && $this->validator->validate("email", "email", ["administrators", "emailinvalid"])) {
            $ticketDepartmentsCount = \WHMCS\Database\Capsule::table("tblticketdepartments")->where("email", "=", $email)->count();
            if($ticketDepartmentsCount) {
                $this->validator->addError(["administrators", "emailCannotBeSupport"]);
            }
        }
    }
    private function validateUsername() : void
    {
        $username = $this->getFormFieldValue("username");
        $username = trim($username ?? "");
        if(empty($username)) {
            return NULL;
        }
        try {
            (new \WHMCS\User\Admin())->validateUsername($username);
            return NULL;
        } catch (\WHMCS\Exception\Validation\InvalidLength $e) {
            $this->validator->addError(["administrators", "usernameLength"]);
        } catch (\WHMCS\Exception\Validation\InvalidFirstCharacter $e) {
            $this->validator->addError(["administrators", "usernameFirstCharacterLetterRequired"]);
        } catch (\WHMCS\Exception\Validation\InvalidCharacters $e) {
            $this->validator->addError(["administrators", "usernameCharacters"]);
        } catch (\WHMCS\Exception\Validation\DuplicateValue $e) {
            $this->validator->addError(["administrators", "userexists"]);
        }
    }
}

?>