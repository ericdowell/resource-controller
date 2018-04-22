<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Http\Controllers;

use EricDowell\ResourceController\Traits\WithMorphModel;

abstract class ModelMorphController extends Controller
{
    use WithMorphModel;
}
