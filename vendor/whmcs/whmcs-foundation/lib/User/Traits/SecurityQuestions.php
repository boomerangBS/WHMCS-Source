<?php
/*
 * @ https://EasyToYou.eu - IonCube v11 Decoder Online
 * @ PHP 7.2 & 7.3
 * @ Decoder version: 1.1.6
 * @ Release: 10/08/2022
 */

// Decoded file for php version 72.
namespace WHMCS\User\Traits;

trait SecurityQuestions
{
    public function hasSecurityQuestion()
    {
        return 0 < $this->securityQuestionId;
    }
    public function getSecurityQuestion()
    {
        $question = $this->securityQuestion;
        if($question) {
            return $question->question;
        }
        return NULL;
    }
    public function setSecurityQuestionAnswerAttribute($value)
    {
        $this->attributes["security_question_answer"] = encrypt($value);
    }
    public function getSecurityQuestionAnswerAttribute()
    {
        return decrypt($this->attributes["security_question_answer"] ?? "");
    }
    public function setSecurityQuestion($questionId, $answer)
    {
        if(!trim($answer)) {
            throw new \WHMCS\Exception\Validation\InvalidValue("An answer is required");
        }
        $this->securityQuestionId = \WHMCS\User\User\SecurityQuestion::findOrFail($questionId)->id;
        $this->securityQuestionAnswer = $answer;
        $this->save();
        return $this;
    }
    public function verifySecurityQuestionAnswer($answer)
    {
        if(!$answer) {
            return false;
        }
        if($this->securityQuestionAnswer === $answer) {
            return true;
        }
        return false;
    }
    public function securityQuestion()
    {
        return $this->belongsTo("WHMCS\\User\\User\\SecurityQuestion");
    }
    public function disableSecurityQuestion()
    {
        $this->securityQuestionId = 0;
        $this->securityQuestionAnswer = "";
        return $this;
    }
}

?>