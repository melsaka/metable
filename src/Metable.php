<?php

namespace Melsaka\Metable;

use Melsaka\Metable\Helpers\MetaRelationship;
use Melsaka\Metable\Helpers\MetaRemovers;
use Melsaka\Metable\Helpers\MetaGetters;
use Melsaka\Metable\Helpers\MetaSetters;
use Melsaka\Metable\Helpers\MetaClauses;
use Illuminate\Support\Arr;

trait Metable
{
    use MetaRelationship, MetaGetters, MetaSetters, MetaRemovers, MetaClauses;

    public function meta($key = null, $default = null)
    {
        if (is_array($key) && Arr::isAssoc($key)) {
            return $this->setMeta($key);
        }

        if ($key === null) {
            return $this->getAllMeta();
        }

        return $this->getMeta($key, $default);
    }

    public function hasMeta($key, $getNullVlaue = false)
    {
        $meta = $this->metaList()->where('key', $key)->first();

        if ($getNullVlaue) {
            return (bool) ($meta);
        }
    
        return (bool) $meta && $meta->value;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->deleteAllMeta();            
        });
    }
}
