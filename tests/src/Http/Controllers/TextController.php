<?php

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use EricDowell\ResourceController\Tests\Models\Text;
use EricDowell\ResourceController\Http\Controllers\ModelMorphController;

class TextController extends ModelMorphController
{
    /**
     * @var string
     */
    protected $morphModel = Text::class;

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @return array
     */
    protected function beforeStoreModel(FormRequest $request): array
    {
        return [
            'user_id' => $request->input('user_id'),
        ];
    }

    /**
     * @param \Illuminate\Foundation\Http\FormRequest $request
     * @param \Illuminate\Database\Eloquent\Model $instance
     */
    protected function beforeModelUpdate(FormRequest $request, Model &$instance): void
    {
        // Optional
    }
}
