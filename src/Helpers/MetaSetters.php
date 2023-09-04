<?php

namespace Melsaka\Metable\Helpers;

use Melsaka\Metable\Helpers\MetaType;

trait MetaSetters
{
    public function setMeta($key, $value = MetaType::META_NOVAL)
    {
        if(is_array($key)) {
            $newMeta = [];

            $updatedMeta = [];

            foreach ($key as $metaKey => $metaValue) {
                $meta = (bool) $this->metaList()->where('key', $metaKey)->first();

                $meta ? $updatedMeta[$metaKey] = $metaValue : $newMeta[$metaKey] = $metaValue;
            }

            $created = false;

            $updated = false;

            if(count($newMeta)) {
                $created = $this->createMeta($newMeta);
            }

            if(count($updatedMeta)) {
                $updated = $this->updateMeta($updatedMeta);
            }

            return $created || $updated;
        }

        $meta = $this->metaList()->where('key', $key)->first();

        if($meta) {
            return $this->updateMeta($key, $value);
        }

        return $this->createMeta($key, $value);
    }

    public function addMeta($key, $value = MetaType::META_NOVAL)
    {
        return $this->createMeta($key, $value);
    }

    public function editMeta($key, $value = MetaType::META_NOVAL)
    {
        return $this->updateMeta($key, $value);
    }

    protected function createMeta($key, $value = MetaType::META_NOVAL)
    {
        if (is_array($key)) {
            $newMeta = [];

            foreach ($key as $metaKey => $metaValue) {
                $isMetaExist = (bool) $this->metaList()->where('key', $metaKey)->first();

                $isMetaExist ?: $newMeta[] = ['key' => $metaKey, 'value' => $metaValue];
            }

            if (count($newMeta)) {
                $createdMeta = $this->metaQuery()->createMany($newMeta);

                $this->updateMetaList($this->metaList()->concat($createdMeta));

                return true;
            }

            return false;
        }

        if ($value === MetaType::META_NOVAL) {
            return false;
        }

        $isMetaExist = (bool) $this->metaList()->where('key', $key)->first();

        if ($isMetaExist) {
            return false;
        }

        $newMeta = $this->metaQuery()->create([
                        'key'       => $key,
                        'value'     => $value,
                    ]);

        $this->metaList()->push($newMeta);

        return true;
    }

    protected function updateMeta($key, $value = MetaType::META_NOVAL)
    {
        if (is_array($key)) {

            $updatedMeta = [];

            foreach ($key as $metaKey => $metaValue) {
                $meta = $this->metaList()->where('key', $metaKey)->first();

                if (!$meta) {
                    continue;
                }

                $updatedMeta[] = [
                    'key'           => $metaKey,
                    'parent_id'     => $meta->parent_id,
                    'parent_type'   => $meta->parent_type,
                    'value'         => MetaType::encode($metaValue),
                    'type'          => MetaType::guessType($metaValue)
                ];

                $meta->value = $metaValue;
            }

            return count($updatedMeta) ? (bool) $this->metaQuery()->upsert($updatedMeta, ['id'], ['value', 'type']) : false;
        }

        if ($value === MetaType::META_NOVAL) {
            return false;
        }

        $meta = $this->metaList()->where('key', $key)->first();

        if(!$meta) {
            return false;
        }

        $meta->value = $value;

        $meta->save();

        return true;
    }

    public function increaseMeta($key, $increase)
    {
        return $this->increaseOrDecreaseMeta($key, $increase);
    }

    public function decreaseMeta($key, $decrease)
    {
        return $this->increaseOrDecreaseMeta($key, $decrease, 'decrease');
    }

    public function increaseOrDecreaseMeta($key, $num, $type = 'increase')
    {
        $meta = $this->metaList()->where('key', $key)->first();

        if ($meta->type === MetaType::META_INTEGER || $meta->type === MetaType::META_DOUBLE) {
            $meta->value =  $type == 'increase' ?
                            $meta->value + $num :
                            $meta->value - $num;

            $meta->save();

            return $meta->value;
        }

        return null;
    }
}
