<?php

namespace Melsaka\Metable\Helpers;

use Melsaka\Metable\Helpers\MetaCollection;

trait MetaGetters
{
    private $metaCollection;

    public function __get($property)
    {
        if ($property === "meta") {
            return $this->metaCollection = $this->metaCollection ?? new MetaCollection($this);
        }

        return parent::__get($property);
    }

    public function getAllMeta()
    {
        return $this->getSelectedMetas();
    }

    public function getMeta($key, $default = null)
    {
        if(is_array($key)) {
            return $this->getSelectedMetas($key);
        }

        $meta = $this->metaList()->where('key', $key)->first();

        $value = $meta ? $meta->value : null;

        if ($value === null) {
            return $default;
        }

        return $value;
    }

    protected function getSelectedMetas($keys = [])
    {
        return $keys ? $this->metaList()->whereIn('key', $keys) : $this->metaList();
    }

    public function getMetableTable()
    {
        $defaultTable = config('metable.tables.default', 'meta');

        return $this->metableTable ?: $defaultTable;
    }
}
