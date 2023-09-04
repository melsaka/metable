<?php

namespace Melsaka\Metable\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class MetaType
{
    public const META_COLLECTION    = "collection";
    public const META_MODEL         = "model";
    public const META_OBJECT        = "object";
    public const META_ARRAY 	    = "array";
    public const META_JSON 		    = "json";
    public const META_STRING 		= "string";
    public const META_INTEGER 		= "integer";
    public const META_DOUBLE 		= "double";
    public const META_BOOLEAN 		= "boolean";
    public const META_NULL          = "null";
    public const META_NOVAL         = "NOVAL";

    public static function guessType($value)
    {
        $valueType = gettype($value);

        if ($value instanceof Collection) {
            return $value->count() ? static::META_COLLECTION : static::META_NULL;
        }

        if ($value instanceof Model) {
            return static::META_MODEL;
        }

        if ($valueType === 'object') {
            return static::META_OBJECT;
        }

        if ($valueType === 'array') {
            return $value === [] ? static::META_NULL : static::META_ARRAY;
        }

        if ($valueType === 'boolean') {
            return static::META_BOOLEAN;
        }

        if ($valueType === 'integer') {
            return static::META_INTEGER;
        }

        if ($valueType === 'double') {
            return static::META_DOUBLE;
        }

        if ($valueType === 'NULL') {
            return static::META_NULL;
        }

        if (static::isJson($value)) {
            $jsonData = json_decode($value, true);
            return empty($jsonData) ? static::META_NULL : static::META_JSON;
        }
        
        return empty($value) ? static::META_NULL : static::META_STRING;
    }

    public static function encode($data)
    {
        $type = static::guessType($data);

        if (static::isObject($type)) {
            return static::encodeClass($data, $type);
        }

        if ($type === static::META_ARRAY) {
            return json_encode($data);
        }

        if ($type === static::META_BOOLEAN) {
            return $data ? 'true' : 'false';
        }

        if ($type === static::META_NULL) {
            return static::META_NULL;
        }

        return $data;
    }

    public static function decode($data, string $type)
    {
        if (static::isObject($type)) {
            return static::decodeClass($data, $type);
        }

        if ($type === static::META_ARRAY) {
            return json_decode($data, true);
        }

        if ($type === static::META_BOOLEAN) {
            return filter_var($data, FILTER_VALIDATE_BOOLEAN);
        }

        if ($type === static::META_NULL) {
            return null;
        }

        if ($type === static::META_INTEGER) {
            return intval($data);
        }

        if ($type === static::META_DOUBLE) {
            return floatval($data);
        }

        return $data;
    }

    private static function encodeClass($class, $type)
    {
        if ($type === static::META_COLLECTION) {
            $firstObj = $class->first();

            if ($firstObj instanceof Model) {
                $name = '\\'. get_class($firstObj);

                $hiddenFields = $firstObj->getHidden();

                $attributes = [];

                foreach ($class as $model) {
                    $attributes[] = array_diff_key($model->getAttributes(), array_flip($hiddenFields));
                }

                return json_encode([
                    '__name'        => $name,
                    '__attributes'  => $attributes
                ]);
            }

            return json_encode($class->toArray());
        }

        if ($type === static::META_MODEL) {
            $attributes = array_diff_key($class->getAttributes(), array_flip($class->getHidden()));
            $name = '\\'. get_class($class);

            return json_encode([
                '__name'        => $name,
                '__attributes'  => $attributes
            ]);
        }

        return serialize($class);
    }

    private static function decodeClass($encodedClass, $type)
    {
        if ($type === static::META_COLLECTION) {
            $class = json_decode($encodedClass, true);

            if (is_array($class) && array_key_exists('__name', $class)) {
                return $class['__name']::hydrate($class['__attributes']);
            }

            return collect($class);
        }

        if ($type === static::META_MODEL) {
            $class = json_decode($encodedClass, true);
            return $class['__name']::hydrate([$class['__attributes']])->first();
        }

        if (@unserialize($encodedClass) === false) {
            return (object) [];
        }

        return unserialize($encodedClass);
    }

    private static function isObject($type)
    {
        return  $type === static::META_COLLECTION   ||
                $type === static::META_MODEL        ||
                $type === static::META_OBJECT;
    }

    private function isJson($string)
    {
        return (bool) preg_match('/^\s*(\{.*\}|\[.*\])\s*$/s', $string);
    }
}
