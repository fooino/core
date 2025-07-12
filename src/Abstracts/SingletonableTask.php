<?php

namespace Fooino\Core\Abstracts;

abstract class SingletonableTask
{
    protected $data;

    public function run(): mixed
    {
        $this->setData();
        return $this->data;
    }

    public function setData(): self
    {
        if (
            blank($this->data)
        ) {
            $this->data = $this->getData();
        }
        return $this;
    }

    public function reset(): self
    {
        $this->data = null;
        return $this;
    }

    abstract public function getData(): mixed;
}
