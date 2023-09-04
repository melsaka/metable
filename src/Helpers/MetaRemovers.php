<?php

namespace Melsaka\Metable\Helpers;

trait MetaRemovers
{
    public function removeMeta($key)
    {
        return $this->deleteMeta($key);
    }

    public function deleteMeta($key)
    {
        if (!is_array($key)) {
            return (bool) $this->metaQuery()->where('key', $key)->delete();
        }

        $noErrors = true;

        foreach($key as $metaKey) {
            $this->metaQuery()->where('key', $metaKey)->delete() ?: $noErrors = false;
        }

        return $noErrors;
    }

    public function deleteAllMeta()
    {
        return (bool) $this->metaQuery()->delete();
    }
}
