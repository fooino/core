<?php

namespace Fooino\Core\Traits;

use Illuminate\Support\Carbon;
use Fooino\Core\Facades\Date;
use Illuminate\Database\Eloquent\Builder;
use DateTimeZone;

trait Dateable
{
    private DateTimeZone|null $timezone = null;

    public function getCreatedAtAttribute($value)
    {
        return $value ?: '';
    }
    public function getUpdatedAtAttribute($value)
    {
        return $value ?: '';
    }
    public function getDeletedAtAttribute($value)
    {
        return $value ?: '';
    }
    public function getExpireAtAttribute($value)
    {
        return $value ?: '';
    }
    public function getExpiredAtAttribute($value)
    {
        return $value ?: '';
    }

    public function getCreatedAtTzAttribute()
    {
        return $this->dateAtTz('created_at');
    }

    public function getUpdatedAtTzAttribute()
    {
        return $this->dateAtTz('updated_at');
    }

    public function getDeletedAtTzAttribute()
    {
        return $this->dateAtTz('deleted_at');
    }

    public function getExpireAtTzAttribute()
    {
        return $this->dateAtTz('expire_at');
    }

    public function getExpiredAtTzAttribute()
    {
        return $this->dateAtTz('expired_at');
    }

    public function dateAtTz(
        string $property,
        string $format = 'Y-m-d H:i:s'
    ): string {

        return Date::convert(
            date: $this?->{$property} ?? '',
            format: $format,
            to: $this->getTimezone()
        );
    }

    public function dateAtAgo(string $property): string
    {
        $property = $this?->{$property} ?? '';
        return (filled($property)) ? Carbon::parse(time: $property)->diffForHumans() : "";
    }

    public function scopeCreatedAt(Builder $query, string|null $createdAt = null): void
    {
        if (
            filled($createdAt)
        ) {
            $query->whereDate('created_at', $createdAt);
        }
    }

    public function scopeCreatedDate(Builder $query, string|null $createdDate = null): void
    {
        if (
            filled($createdDate)
        ) {
            $query->where('created_date', $createdDate);
        }
    }

    public function scopeWhereDateBetween(
        Builder $query,
        string $field,
        string|null $from = null,
        string|null $to = null
    ): void {

        if (
            filled($from)
        ) {
            $query->whereDate($field, '>=', $from);
        }
        if (
            filled($to)
        ) {
            $query->whereDate($field, '<=', $to);
        }
    }

    public function scopeWhereFieldBetween(
        Builder $query,
        string $field,
        string|null $from = null,
        string|null $to = null
    ): void {

        if (
            filled($from)
        ) {
            $query->where($field, '>=', $from);
        }
        if (
            filled($to)
        ) {
            $query->where($field, '<=', $to);
        }
    }

    public function scopeTodayCreated(Builder $query): void
    {
        $query->whereDate('created_at', \date('Y-m-d'));
    }

    public function scopeYesterdayCreated(Builder $query): void
    {
        $query->whereDate('created_at', \date('Y-m-d', \strtotime('yesterday')));
    }

    public function scopeTodayCreatedDate(Builder $query): void
    {
        $query->where('created_date', \date('Y-m-d'));
    }
    public function scopeYesterdayCreatedDate(Builder $query): void
    {
        $query->where('created_date', \date('Y-m-d', \strtotime('yesterday')));
    }

    public function scopeThisMonthCreated(Builder $query): void
    {
        $query->whereDateBetween(
            field: 'created_at',
            from: \date('Y-m-d', \strtotime('first day of this month')),
            to: \date('Y-m-d', \strtotime('last day of this month'))
        );
    }

    public function scopeThisMonthCreatedDate(Builder $query): void
    {
        $query->whereDateBetween(
            field: 'created_date',
            from: \date('Y-m-d', \strtotime('first day of this month')),
            to: \date('Y-m-d', \strtotime('last day of this month'))
        );
    }

    public function scopeLast30DaysCreated(Builder $query): void
    {
        $query->whereDateBetween(
            field: 'created_at',
            from: \date('Y-m-d', \strtotime('-30 days')),
            to: \date('Y-m-d', \strtotime('today'))
        );
    }

    public function scopeLast30DaysCreatedDate(Builder $query): void
    {
        $query->whereDateBetween(
            field: 'created_date',
            from: \date('Y-m-d', \strtotime('-30 days')),
            to: \date('Y-m-d', \strtotime('today'))
        );
    }

    public function scopeThisYearCreated(Builder $query): void
    {
        $query->whereYear('created_at', \date('Y'));
    }

    public function scopeThisYearCreatedDate(Builder $query): void
    {
        $query->whereYear('created_date', \date('Y'));
    }

    public function getTimezone(): DateTimeZone
    {
        $this->timezone = $this->timezone ?: new DateTimeZone(getUserTimezone());
        return $this->timezone;
    }
}
