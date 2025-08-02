<?php

namespace Fooino\Core\Models;

use Fooino\Core\{
    Enums\Direction,
    Enums\LanguageState,
    Enums\LanguageStatus,
    Traits\Infoable,
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
        Infoable,
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
        return app()->environment() == 'testing' ? __DIR__ . "/../../assets/languages/{$this->code}.png" : asset("vendor/fooino/core/languages/{$this->code}.png");
    }

    public function getDirectionDetailAttribute()
    {
        return Direction::from(value: $this->direction)->detail();
    }

    public function getStatusDetailAttribute()
    {
        return LanguageStatus::from(value: $this->status)->detail();
    }

    public function getStatusesAttribute()
    {
        return LanguageStatus::statuses(id: $this->id);
    }

    public function getStateDetailAttribute()
    {
        return LanguageState::from(value: $this->state)->detail();
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

    public function scopeStatus(Builder $query, LanguageStatus|string|null $status = null): void
    {
        if (
            filled($status)
        ) {
            $query->where('status', enumOrValue($status));
        }
    }

    public function scopeActive(Builder $query): void
    {
        $query->status(LanguageStatus::ACTIVE);
    }

    public function scopeInactive(Builder $query): void
    {
        $query->status(LanguageStatus::INACTIVE);
    }

    public function scopeState(Builder $query, LanguageState|string|null $state = null): void
    {
        if (
            filled($state)
        ) {
            $query->where('state', enumOrValue($state));
        }
    }

    public function scopeDefault(Builder $query): void
    {
        $query->state(LanguageState::DEFAULT);
    }

    public function scopeNonDefault(Builder $query): void
    {
        $query->state(LanguageState::NON_DEFAULT);
    }

    /**
     * custom functions section
     */

    public function getIdSort(): string
    {
        return 'ASC';
    }
}
