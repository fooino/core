<?php

namespace Fooino\Core\Models;

use Fooino\Core\{
    Enums\Direction,
    Enums\LanguageState,
    Traits\Modelable,
    Traits\Searchable,
    Traits\Dateable,
    Traits\Loggable,
    Traits\Prioritiable
};
use Illuminate\Database\Eloquent\{
    Builder,
    Model,
    SoftDeletes,
    Casts\Attribute
};


class Language extends Model
{
    use
        Modelable,
        Searchable,
        Dateable,
        Loggable,
        Prioritiable,
        SoftDeletes;

    protected $guarded = ['id'];

    protected $permissions = [
        'show',
        'update'
    ];


    /**
     * relationships section
     */

    /**
     * attributes section
     */
    protected function timezones(): Attribute
    {
        return jsonAttribute();
    }

    protected function code(): Attribute
    {
        return Attribute::make(
            get: fn($value) => emptyToNullOrValue(strtolower((string) $value)),
            set: fn($value) => emptyToNullOrValue(strtolower((string) $value))
        );
    }

    public function getFlagAttribute()
    {
        return app()->environment() == 'testing' ? __DIR__ . "/../../assets/language/flags/{$this->code}.png" : asset("vendor/fooino/core/language/flags/{$this->code}.png");
    }

    public function getDirectionDetailAttribute()
    {
        return Direction::from(value: $this->direction)->detail();
    }

    public function getEditableAttribute()
    {
        return (int) ($this->state == LanguageState::NON_DEFAULT->value);
    }

    /**
     * scopes section
     */

    public function scopeSearch(Builder $query, string|int|float|bool|null $search = null): void
    {
        if (
            filled($search)
        ) {
            $query->where(
                fn($q) => $q
                    ->orWhere('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
            );
        }
    }

    public function scopeCodeFilter(Builder $query, string|int|float|bool|null $code = null): void
    {
        if (
            filled($code)
        ) {
            $query->where('code', $code);
        }
    }

    public function scopeDirection(Builder $query, Direction|string|null $direction = null): void
    {
        if (
            filled($direction)
        ) {
            $query->where('direction', enumOrValue($direction));
        }
    }

    public function scopeRTL(Builder $query): void
    {
        $query->direction(Direction::RTL);
    }

    public function scopeLTR(Builder $query): void
    {
        $query->direction(Direction::LTR);
    }

    /**
     * custom functions section
     */

    public function getIdSort(): string
    {
        return 'ASC';
    }
}
