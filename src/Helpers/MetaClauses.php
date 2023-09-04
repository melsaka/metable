<?php

namespace Melsaka\Metable\Helpers;

use Melsaka\Metable\Helpers\MetaType;

trait MetaClauses
{
    private $countOfMetaJoins = 0;

    public function scopeOrderByMeta($query, $key, $direction = 'asc')
    {
        $this->countOfMetaJoins += 1;

        $table = $this->getMetableTable();

        return $query->leftJoin($table . ' as meta' . $this->countOfMetaJoins, function ($q) use ($key) {
            $q->on('meta' . $this->countOfMetaJoins . '.parent_id', '=', $this->getTable() . ".id");
            $q->where('meta' . $this->countOfMetaJoins . '.parent_type', '=', static::class);
            $q->where('meta' . $this->countOfMetaJoins . '.key', $key);
        })
            ->orderByRaw("CASE (meta" . $this->countOfMetaJoins . ".key)
              WHEN '$key' THEN 1
              ELSE 0
              END
              DESC")
            ->orderBy('meta' . $this->countOfMetaJoins . '.value', strtoupper($direction))
            ->select($this->getTable() . ".*");
    }

    public function scopeWhereMeta($query, $key, $operator = null, $value = MetaType::META_NOVAL)
    {
        return $this->whereMetaProccess($query, $key, $operator, $value);
    }

    public function scopeOrWhereMeta($query, $key, $operator = null, $value = MetaType::META_NOVAL)
    {
        return $this->whereMetaProccess($query, $key, $operator, $value, true);
    }

    private function whereMetaProccess($query, $key, $operator, $value, $orWhere = false)
    {
        $methodType = $orWhere ? 'orWhereHas' : 'whereHas';

        $relation = static::metaRelationName();

        if (is_array($key)) {

            $conditions = $key;

            foreach ($conditions as $condition) {

                if(!is_array($condition)) {

                    list($conditionKey, $conditionOperator, $conditionValue) = $this->extractKeyOperatorValue($conditions);

                    $query = call_user_func_array([$this, 'scopeWhereMetaTest'], [$query, $conditionKey, $conditionOperator, $conditionValue, $orWhere]);

                    continue;
                }

                list($conditionKey, $conditionOperator, $conditionValue) = $this->extractKeyOperatorValue($condition);

                $query = call_user_func_array([$this, 'scopeWhereMetaTest'], [$query, $conditionKey, $conditionOperator, $conditionValue, $orWhere]);
            }
        }

        return $query->{$methodType}($relation, function ($metaQuery) use($key, $value, $operator){
            if ($value === MetaType::META_NOVAL) {
                $value = $operator;
                $operator = '=';
            }

            $metaQuery->where('key', $key)->where('value', $operator, $value);
        });
    }

    private function extractKeyOperatorValue($conditionArray)
    {
        $key = $condition[0];

        $operator = array_key_exists(2, $condition) ? $condition[1] : '=';

        $value = array_key_exists(2, $condition) ? $condition[2] : $condition[1];

        return [$key, $operator, $value];
    } 

    public function scopeWhereMetaIn($query, $key, $values, $in = true)
    {
        return $this->whereMetaInProccess($query, $key, $values);
    }

    public function scopeWhereMetaNotIn($query, $key, $values)
    {
        return $this->whereMetaInProccess($query, $key, $values, false);
    }

    public function whereMetaInProccess($query, $key, $values, $in = true)
    {
        $relation = static::metaRelationName();

        $methodType = $in ? 'whereIn' : 'whereNotIn';

        return $query->whereHas($relation, function ($query) use ($methodType, $key, $values, $in) {
            if ($in) {
                return $query->where('key', $key)->whereIn('value', $values);
            }

            $query->where('key', $key)->whereNotIn('value',  $values);
        });
    }

    public function scopeWhereMetaNull($query, $key)
    {
        $this->whereMetaNullProccess($query, $key);
    }

    public function scopeOrWhereMetaNull($query, $key)
    {
        return $this->whereMetaNullProccess($query, $key, true);
    }

    public function scopeWhereMetaNotNull($query, $key)
    {
        $this->whereMetaNullProccess($query, $key, false, '<>');
    }

    public function scopeOrWhereMetaNotNull($query, $key)
    {
        return $this->whereMetaNullProccess($query, $key, true, '<>');
    }

    private function whereMetaNullProccess($query, $key, $orWhere = false, $operator = '=')
    {
        $relation = static::metaRelationName();

        $methodType = $orWhere ? 'orWhereHas' : 'whereHas';

        return $query->{$methodType}($relation, function ($query) use ($key, $operator) {
            $query->where('key', $key)->where('type', $operator, MetaType::META_NULL);
        });
    }

    public function scopeWhereMetaHas($query, $key = null, $countNull = false)
    {
        $this->whereMetaHasProccess($query, $key, $countNull);
    }

    public function scopeOrWhereMetaHas($query, $key = null, $countNull = false)
    {
        $this->whereMetaHasProccess($query, $key, $countNull, true);
    }

    private function whereMetaHasProccess($query, $key, $countNull, $orWhere = false)
    {
        $relation = static::metaRelationName();

        $methodType = $orWhere ? 'orWhereHas' : 'whereHas';

        if ($key === null) {
            return $query->{$methodType}($relation);
        }

        return $query->{$methodType}($relation, function ($query) use ($key, $countNull) {
            $query->where('key', $key);
            
            if (!$countNull) {
                $query->where('type', '<>', MetaType::META_NULL);
            }
        });
    }

    public function scopeWhereMetaDoesntHave($query, $key = null, $countNull = false)
    {
        $this->whereMetaDoesntHaveProccess($query, $key, $countNull);
    }

    public function scopeOrWhereMetaDoesntHave($query, $key = null, $countNull = false)
    {
        $this->whereMetaDoesntHaveProccess($query, $key, $countNull, true);
    }

    public function whereMetaDoesntHaveProccess($query, $key, $countNull, $orWhere = false)
    {
        $relation = static::metaRelationName();

        $methodType = $orWhere ? 'orWhereDoesntHave' : 'whereDoesntHave';

        if ($key === null) {
            return $query->{$methodType}($relation);
        }

        return $query->{$methodType}($relation, function ($query) use ($key, $countNull) {
            $query->where('key', $key);
            
            if ($countNull) {
                $query->where('type', '<>', MetaType::META_NULL);
            }
        });
    }
}
