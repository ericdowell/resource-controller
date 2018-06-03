<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Exceptions;

use RuntimeException;
use Illuminate\Database\Eloquent\Model;

class ModelClassCheckException extends RuntimeException
{
    /**
     * Full classname of the Eloquent model.
     *
     * @var string
     */
    protected $model;

    /**
     * Return classname of Eloquent model.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Return instance of Eloquent model.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelInstance(): Model
    {
        return new $this->model();
    }

    /**
     * Set the Eloquent model to check.
     *
     * @param string $model
     *
     * @return $this
     */
    public function setModel($model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Check if model property is null and update error message.
     *
     * @return bool
     */
    protected function isNull(): bool
    {
        $this->message = 'Model property is empty.';

        return is_null($this->model);
    }

    /**
     * Check if model exists and extends Eloquent model base class.
     *
     * @return bool
     */
    public function classExists(): bool
    {
        if ($this->isNull()) {
            return false;
        }
        $this->message = "Model [{$this->model}] does not exist.";

        return class_exists($this->model) && in_array(Model::class, class_parents($this->model));
    }
}
