<?php

namespace Fooino\Core\Traits;

use Fooino\Core\Facades\Json;
use Fooino\Core\Tasks\Tag\AddNewTagTask;

trait Seoable
{
    public function getMetaKeywordsAttribute($value)
    {
        return filled($value) ? Json::decodeToArray($value) : [];
    }

    public function getMetaTitleAttribute($value)
    {
        return (string) $value;
    }

    public function getMetaDescriptionAttribute($value)
    {
        return (string) $value;
    }

    public function getCanonicalAttribute($value)
    {
        return (string) $value;
    }

    public function getSlugAttribute($value)
    {
        return (string) $value;
    }

    public function setMetaTitleAttribute($value)
    {
        $this->attributes['meta_title'] = $value ?: null;
    }

    public function setMetaDescriptionAttribute($value)
    {
        $this->attributes['meta_description'] = $value ?: null;
    }

    public function setMetaKeywordsAttribute($value)
    {
        if (
            \is_array($value)
        ) {
            $value = \array_unique($value);
            app(AddNewTagTask::class)->run(tags: $value);
        }
        $this->attributes['meta_keywords'] = filled($value) ? Json::encode($value) : null;
    }

    public function setCanonicalAttribute($value)
    {
        $this->attributes['canonical'] = prettifyCanonical($value);
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = prettifySlug($value);
    }

    public function getMetaKeywordsToStringAttribute()
    {
        return \implode(',', (array) $this->meta_keywords);
    }

    public function getSeoResponseAttribute()
    {
        return [
            'slug'                      => $this->slug,
            'meta_title'                => $this->meta_title,
            'meta_description'          => $this->meta_description,
            'meta_keywords'             => $this->meta_keywords,
            'meta_keywords_to_string'   => $this->meta_keywords_to_string,
            'canonical'                 => $this->canonical,
        ];
    }
}
