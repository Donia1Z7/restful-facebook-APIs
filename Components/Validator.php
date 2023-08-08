<?php

namespace Components;

use Constants\Rules;
use Mixin\BasicRulesValidation;
use Illuminate\Database\Eloquent\Model;
use Mixin\DatabaseRulesValidation;
use Models\User;

class Validator
{
    use BasicRulesValidation,DatabaseRulesValidation;


    public function __construct()
    {
        $this->validateImplementedRulesConstants();
    }

    /**
     * @throws \Exception
     */
    private function validateRuleIsImplemented($rule, $exceptionMessage)
    {

        $rule_method = "validate_rule_is_" . $rule;

        if (!method_exists($this, $rule_method)) {
            throw new \Exception($exceptionMessage);
        }
    }

    private function validateImplementedRulesConstants()
    {

        $rules = (new Rules())->listOfExistsConstants();
        foreach ($rules as $rule) {

            $this->validateRuleIsImplemented(
                $rule,
                "Please sync your Rules constants with existing implementations in Validator component.");
        }
    }


    /**
     * @throws \Exception
     */
    private function validate($schema, $values, $level,$resourceId=null)
    {

        foreach ($schema as $key => $rules) {

            $value = null;

            if (key_exists($key, $values)) {
                $value = $values[$key];
                 }

            foreach ($rules as $specialRule => $rule){

                $arguments = [$key,$value,$level];

                if(is_array($rule) && $rule == Rules::UNIQUE){

                    throw new \Exception("UNIQUE Rule should have another level of data having `model` value.");

                }else if (is_array($rule)) {

                    if (key_exists("model", $rule)) {

                        $arguments[] = $rule["model"];
                        $arguments[]  =$resourceId;
                    }
                    $rule = $specialRule;
                }

                $this->validateRuleIsImplemented($rule,
                    " $rule isn't implemented , please use rules constant class to skip this kind of errors.");

                $rule_method = "validate_rule_is_" . $rule;

                $this->$rule_method(... $arguments);
            }
        }
    }

    public function validateUrlVariables($schema, $values)
    {
        $this->validate($schema, $values, "url variables level");

    }

    public function validateQueryParams($schema, $values,$resourceId)
    {
        $this->validate($schema, $values, "query params level",$resourceId);

    }


    public function validateRequestPayload($schema, $values,$resourceId)
    {

        $this->validate($schema, $values, "request payload level",$resourceId);

    }

}