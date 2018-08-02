<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\With;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

trait MorphModelProps
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
     * @var Eloquent|Builder
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
     * @param Eloquent $morphModelInstance
     *
     * @return $this
     */
    protected function setMorphModelInstance(Eloquent $morphModelInstance): self
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
