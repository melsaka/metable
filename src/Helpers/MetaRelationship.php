<?php

namespace Melsaka\Metable\Helpers;

use Melsaka\Metable\Models\Meta;

trait MetaRelationship
{
    public function metaData()
    {
        $instance = new Meta();

        $instance->setTable($this->getMetableTable());

        [$type, $id] = $this->getMorphs('parent', null, null);

        $table = $instance->getTable();

        return $this->newMorphMany($instance->newQuery(), $this, $table.'.'.$type, $table.'.'.$id, $this->getKeyName());
    }

    public static function metaRelationName()
    {
        return 'metaData';
    }

    public function scopeWithMeta($query, $callback = null)
    {
        $relation = static::metaRelationName();

        if ($callback) {
            return $query->with([$relation => $callback]);
        }
        
        return $query->with($relation);
    }

    public function metaQuery()
    {
        $relation = static::metaRelationName();
        return $this->{$relation}();
    }

    public function metaList()
    {
        $relation = static::metaRelationName();
        return $this->{$relation};
    }

    public function updateMetaList($updatedList)
    {
        $relation = static::metaRelationName();
        $this->{$relation} = $updatedList;
    }
}
