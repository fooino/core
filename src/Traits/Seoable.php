<?php

namespace Fooino\Core\Traits;

use Fooino\Core\Tasks\Tag\AddNewTagTask;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait Seoable
{
    public function metaTitle(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string) $value,
            set: fn($value) => emptyToNullOrValue($value)
        );
    }

    public function metaDescription(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string) $value,
            set: fn($value) => emptyToNullOrValue($value)
        );
    }

    public function canonical(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string) $value,
            set: fn($value) => prettifyCanonical($value)
        );
    }

    public function slug(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (string) $value,
            set: fn($value) => prettifySlug($value)
        );
    }

    public function getKeywordsAttribute($value)
    {
        return filled($value) ? jsonDecodeToArray($value) : [];
    }


    public function setKeywordsAttribute($value)
    {
        $value = \array_unique((array) emptyToNullOrValue($value));

        app(AddNewTagTask::class)->run(tags: $value);

        $this->attributes['keywords'] = filled($value) ? jsonEncode($value) : null;
    }

    public function getKeywordsToStringAttribute()
    {
        return \implode(',', (array) $this->keywords);
    }

    public function getSeoResponseAttribute()
    {
        return [
            'slug'                      => $this->slug,
            'meta_title'                => $this->meta_title,
            'meta_description'          => $this->meta_description,
            'keywords'                  => $this->keywords,
            'keywords_to_string'        => $this->keywords_to_string,
            'canonical'                 => $this->canonical,
        ];
    }
}
