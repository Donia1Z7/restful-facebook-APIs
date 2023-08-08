<?php

namespace Mixin;

use Helpers\RequestHelper;
use CustomExceptions\BadRequestException;
use Illuminate\Database\Eloquent\Model;

trait DatabaseRulesValidation
{
    /**
     * @throws BadRequestException
     */
    private function validate_rule_is_unique($key, $value, $level, $model, $resourceId){

        if(!$resourceId){
            return;
        }

        if($value == null){
            return;
        }

        $fetchedModel = $model::query()->where($key, $value)->first();

        if($fetchedModel==null){
            return;
        }

        $isUpdatedFlag=true;

        $modelId =$resourceId;

        if ($modelId){

            $matchedModel=$model::query()->find($modelId);

            if($matchedModel->$key==$fetchedModel->$key) {
                $isUpdatedFlag = false;
            }
        }
        if ($fetchedModel && $isUpdatedFlag){
            throw new BadRequestException("$key (in $level) within value = $value should be unique.");
        }

    }

}