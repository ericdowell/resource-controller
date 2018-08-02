<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Traits\With;

trait ModelResourceProps
{
    /**
     * Auth Middleware to apply to non-public routes.
     *
     * @var array
     */
    protected $authMiddleware = ['auth'];

    /**
     * The data passed to the view.
     *
     * @var array
     */
    protected $mergeData = [];

    /**
     * Default Middleware to apply to all routes.
     *
     * @var array
     */
    protected $modelMiddleware = [];

    /**
     * Values used for index pagination.
     *
     * @var array
     */
    protected $paginate = [];

    /**
     * Route names of public actions, Auth Middleware are not applied to these.
     *
     * @var array
     */
    protected $publicActions = [
        'index',
        'show',
    ];

    /**
     * @param string $method
     *
     * @return $this
     */
    protected function editMethodPatch(): self
    {
        return $this->setEditMethod('patch');
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    protected function editMethodPut(): self
    {
        return $this->setEditMethod('put');
    }

    /**
     * @param string $method
     *
     * @return $this
     */
    protected function setEditMethod(string $method): self
    {
        $this->editMethod = $method;

        return $this;
    }

    /**
     * @param bool $allowUpsert
     *
     * @return $this
     */
    protected function setAllowUpsert(bool $allowUpsert): self
    {
        $this->allowUpsert = $allowUpsert;

        return $this;
    }

    /**
     * @param array $upsertExcept
     *
     * @return $this
     */
    protected function setUpsertExcept(array $upsertExcept): self
    {
        $this->upsertExcept = $upsertExcept;

        return $this;
    }
}
