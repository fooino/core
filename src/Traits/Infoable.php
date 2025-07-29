<?php


namespace Fooino\Core\Traits;

trait Infoable
{

    public function objectNamespace(): string
    {
        return once(fn() => get_called_class());
    }

    public function objectPackage(): string
    {
        return once(fn() => explode('\\', $this->objectNamespace())[1] ?? '');
    }

    public function objectClassName(): string
    {
        return once(fn() => class_basename($this->objectNamespace()));
    }

    public function objectUsedTraits(): array
    {
        return once(fn() => class_uses($this));
    }

    public function objectUsedTranslatable(): bool
    {
        return once(
            fn() => in_array(
                'Astrotomic\Translatable\Translatable',
                $this->objectUsedTraits()
            )
        );
    }

    public function objectUsedMediable(): bool
    {
        return once(
            fn() => in_array(
                'Fooino\Media\Traits\Mediable',
                $this->objectUsedTraits()
            )
        );
    }

    public function objectKeyName(): string
    {
        return 'name';
    }

    public function objectName(): array
    {
        $unknown = __(key: 'msg.unknown');
        return [
            'name'  => $this->objectUsedTranslatable() ? (($this?->translateOrDefault(getDefaultLocale())?->{$this->objectKeyName()}) ?? $unknown) : (($this?->{$this->objectKeyName()}) ?? $unknown),
            'type'  => __(key: 'msg.' . lcfirst($this->objectClassName()))
        ];
    }
}
