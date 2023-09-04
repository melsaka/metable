<?php

namespace Melsaka\Metable\Models;

use Illuminate\Database\Eloquent\Model;
use Melsaka\Metable\Helpers\MetaType;

class Meta extends Model
{
    public $isModelSaving;

    protected $originalValue;

    protected $fillable = [
        'type',
        'key',
        'value',
        'owner_id',
        'owner_type'
    ];

    public function __construct(array $attributes = [])
    {
        $tableName = config('metable.tables.default', 'meta');

        parent::__construct($attributes);

        $this->setTable($tableName);
    }

    /**
     * Get the parent meta model (ex: Post, User, etc..).
     */
    public function parent()
    {
        return $this->morphTo();
    }

    public function getValueAttribute($value)
    {
        if (!$this->isModelSaving) {
            return MetaType::decode($value, $this->type);
        }

        return $value;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($meta) {
            $meta->isModelSaving = true;

            $meta->type = MetaType::guessType($meta->value);

            $meta->value = MetaType::encode($meta->value);

            $meta->isModelSaving = false;
        });
    }
}
