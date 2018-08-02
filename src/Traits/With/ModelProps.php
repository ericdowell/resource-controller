<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\With;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

trait ModelProps
{
    /**
     * Current route action based on parsed route name.
     *
     * @var string
     */
    protected $action;

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
     * @var Eloquent|Builder
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
     * Flag for allowing FormRequest requests to return all fields.
     *
     * @var bool
     */
    protected $useRequestValidated = false;

    /**
     * Flag for setting/updating 'user_id' as attribute of Eloquent Model.
     *
     * @var bool
     */
    protected $withUser = false;

    /**
     * @param string $action
     *
     * @return $this
     */
    protected function setFormAction(string $action): self
    {
        $this->formAction = $action;

        return $this->mergeContext($this->mergeData, compact('action'));
    }

    /**
     * @param string $template
     *
     * @return $this
     */
    protected function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    protected function setType(string $type): self
    {
        $this->type = $type;

        return $this->mergeContext($this->mergeData, compact('type'));
    }

    /**
     * @param string typeName
     *
     * @return $this
     */
    protected function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;

        return $this->mergeContext($this->mergeData, compact('typeName'));
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    protected function setTypeAndTypeName(string $type): self
    {
        $this->type = $type;
        $this->typeName = $typeName = str_plural(ucfirst($type));

        return $this->mergeContext($this->mergeData, compact('type', 'typeName'));
    }

    /**
     * @return $this
     */
    protected function includeUser(): self
    {
        $this->withUser = true;

        return $this;
    }

    /**
     * @return $this
     */
    protected function noUser(): self
    {
        $this->withUser = false;

        return $this;
    }

    /**
     * @param string $findModelClass
     *
     * @return $this
     */
    protected function setFindModelClass(string $findModelClass): self
    {
        $this->findModelClass = $findModelClass;

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
     * @param Eloquent $modelInstance
     *
     * @return $this
     */
    protected function setModelInstance(Eloquent $modelInstance): self
    {
        $this->modelInstance = $instance = ${$this->type} = $modelInstance;

        return $this->setModelClass(get_class($modelInstance))
                    ->mergeContext($this->mergeData, compact($this->type, 'instance'));
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
