<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\MorphModel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait WithProperties
{
    /**
     * Parent morph Eloquent Model ::class string output.
     *
     * @var string
     */
    protected $morphModelClass;

    /**
     * Instance of parent morph Eloquent Model.
     *
     * @var Model|Builder
     */
    protected $morphModelInstance;

    /**
     * Property name used to access model instance from parent morph Eloquent Model.
     *
     * @var string
     */
    protected $morphType;

    /**
     * Set parent morph Eloquent Model ::class.
     *
     * @param string $morphModelClass
     *
     * @return self
     */
    protected function setMorphModelClass(string $morphModelClass): self
    {
        $this->morphModelClass = $morphModelClass;

        return $this;
    }

    /**
     * Set parent morph Eloquent Model instance.
     *
     * @param Model $morphModelInstance
     *
     * @return $this
     */
    protected function setMorphModelInstance(Model $morphModelInstance): self
    {
        $this->morphModelInstance = $morphModelInstance;

        return $this->setMorphModelClass(get_class($morphModelInstance));
    }

    /**
     * @param string $morphType
     *
     * @return $this
     */
    protected function setMorphType(string $morphType): self
    {
        $this->morphType = $morphType;

        return $this;
    }
}
