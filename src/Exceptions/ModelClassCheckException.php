<?php

namespace EricDowell\ResourceController\Exceptions;

use RuntimeException;

class ModelClassCheckException extends RuntimeException
{
    /**
     * Name of the affected Eloquent model.
     *
     * @var string
     */
    protected $model;

    /**
     * Set the affected Eloquent model.
     *
     * @param  string $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return bool
     */
    protected function isNull()
    {
        $this->message = 'Model property is empty.';

        return is_null($this->model);
    }

    /**
     * @return bool
     */
    public function classExists()
    {
        if ($this->isNull()) {
            return false;
        }
        $this->message = "Model [{$this->model}] does not exist.";

        return class_exists($this->model);
    }
}
