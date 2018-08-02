<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Controllers;

use EricDowell\ResourceController\Traits\With\MorphModel;

abstract class ModelMorphController extends Controller
{
    use MorphModel;
}
