<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

trait WithProperties
{
    /**
     * Current form action based on parsed route name.
     *
     * @var string
     */
    protected $formAction;

    /**
     * Eloquent Model ::class string output.
     *
     * @var string
     */
    protected $modelClass;

    /**
     * Instance of the Eloquent Model.
     *
     * @var Model|Builder
     */
    protected $modelInstance;

    /**
     * Matches the current route name.
     *
     * @var string
     */
    protected $template;

    /**
     * Model type based on parsed route name.
     *
     * @var string
     */
    protected $type;

    /**
     * Plural version of '$type' property, first letter is uppercase.
     *
     * @var string
     */
    protected $typeName;

    /**
     * Flag for setting/updating 'user_id' as attribute of Eloquent Model.
     *
     * @var bool
     */
    protected $withUser = true;

    /**
     * @return $this
     */
    protected function noUser(): self
    {
        $this->withUser = false;

        return $this;
    }

    /**
     * @param string $modelClass
     *
     * @return $this
     */
    protected function setModelClass(string $modelClass): self
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    /**
     * @param Model $modelInstance
     *
     * @return $this
     */
    protected function setModelInstance(Model $modelInstance): self
    {
        $this->modelInstance = $modelInstance;

        return $this->setModelClass(get_class($modelInstance));
    }

    /**
     * @param string|int $id
     *
     * @return $this
     */
    protected function setUserId($id): self
    {
        $this->userId = $id;

        return $this;
    }

    /**
     * @param array $actionMap
     *
     * @return $this
     */
    protected function setActionMap(array $actionMap): self
    {
        $this->actionMap = $actionMap;

        return $this;
    }

    /**
     * @param array $context
     * @param mixed ...$merge
     *
     * @return $this
     */
    protected function mergeContext(array &$context, ...$merge): self
    {
        // Place $context as first arg when calling array_merge.
        array_unshift($merge, $context);

        $context = call_user_func_array('array_merge', $merge);

        return $this;
    }
}
