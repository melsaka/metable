<?php

namespace Melsaka\Metable\Helpers;

use Melsaka\Metable\Helpers\MetaType;

class MetaCollection
{
    protected $collection;
    protected $updates;
    protected $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->updates = [];
    }

    public function __get($property)
    {
        return $this->parent->getMeta($property);
    }

    public function __set($property, $value)
    {
        $this->updates[$property] = $value;
    }

    public function save()
    {
        try {
            $this->parent->setMeta($this->updates);

            $this->updates = [];

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function all()
    {
        return $this->parent->getAllMeta();
    }

    public function get($key, $default = null)
    {
        return $this->parent->getMeta($key, $default);
    }

    public function add($key, $value = MetaType::META_NOVAL)
    {
        return $this->parent->addMeta($key, $value);
    }

    public function edit($key, $value = MetaType::META_NOVAL)
    {
        return $this->parent->editMeta($key, $value);
    }

    public function set($key, $value = MetaType::META_NOVAL)
    {
        return $this->parent->setMeta($key, $value);
    }

    public function has($key, $getNullVlaue = false)
    {
        return $this->parent->hasMeta($key, $getNullVlaue);
    }

    public function delete($key)
    {
        return $this->parent->deleteMeta($key);
    }

    public function deleteAll()
    {
        return $this->parent->deleteAllMeta();
    }

    public function increase($key, $increase)
    {
        return $this->parent->increaseMeta($key, $increase);
    }

    public function decrease($key, $decrease)
    {
        return $this->parent->decreaseMeta($key, $decrease);
    }
}
