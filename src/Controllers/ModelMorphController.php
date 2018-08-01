<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Controllers;

use EricDowell\ResourceController\Traits\WithMorphModel;

abstract class ModelMorphController extends Controller
{
    use WithMorphModel;
}
