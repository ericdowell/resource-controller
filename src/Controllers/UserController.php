<?php

declare(strict_types=1);

namespace ResourceController\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

abstract class UserController extends AbstractModelController
{
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
