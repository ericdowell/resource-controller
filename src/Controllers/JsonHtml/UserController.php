<?php

declare(strict_types=1);

namespace ResourceController\Controllers\JsonHtml;

use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;
use ResourceController\Traits\Controllers\PaginateIndex;

class UserController extends AbstractModelController
{
    use PaginateIndex;

    /**
     * Get attributes from validated request.
     *
     * @param  \Illuminate\Foundation\Http\FormRequest  $request
     * @return array
     */
    protected function getRequestAttributes(FormRequest $request): array
    {
        $attributes = parent::getRequestAttributes($request);

        if (isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        return $attributes;
    }
}
