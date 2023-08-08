<?php

namespace Helpers;
use CustomExceptions\ResourceNotFound;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Models\User;
use newrelic\DistributedTracePayload;

class ResourceHelper
{
    public static function loadOnly($attributes, $resource) {

        if (!is_array($attributes)) {

            throw new Exception("[Bad usage] the passed `attributes` should be array.");
        }
        if (! ($resource instanceof Model)) {

            throw new Exception("[Bad usage] the passed `resource` method should be instance of Eloquent\Model.");
        }

        $loaded_data = [];
        foreach ($attributes as $attribute) {

            $loaded_data[$attribute] = $resource->$attribute;
        }

        return $loaded_data;
    }

    public static function loadOnlyForList($attributes, $resources) {

        $loaded_collection = [];
        foreach ($resources as $resource) {

            $loaded_collection[] = self::loadOnly($attributes, $resource);
        }
        return $loaded_collection;
    }

    public static function getPaginatedResource($model,$columns,$relationWith = [])
    {
        $limit=key_exists("limit",$_GET) ? $_GET["limit"] : 10 ;
        $current_page=key_exists("page",$_GET) ? $_GET["page"] : 1;

        $query=$model;

        if(gettype($model)=="string"){
            $query = $model::query();
        }
        if (!empty($relationWith)) {
            $query->with($relationWith);
        }

        $paginator = $query->paginate($limit, $columns, 'page', $current_page);

        return $paginator->items();

    }


    public static function findResource($model,$resourceId,$with =[]){

        if(! ((new $model) instanceof Model)){
            throw new Exception("[Bad usage] the passed `model` within `findResource` method should be subclass of Eloquent\Model.");

        }
        return $model::query()->with($with)->find($resourceId);
    }
    public static function findResourceOr404Exception($model, $resourceId,$with = []) {

        $resource = self::findResource($model, $resourceId,$with);
        if (! $resource) {

            throw new ResourceNotFound();
        }

        return $resource;
    }
}