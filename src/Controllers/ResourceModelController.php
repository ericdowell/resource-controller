<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Controllers;

use EricDowell\ResourceController\Traits\WithModelResource;

abstract class ResourceModelController extends Controller
{
    use WithModelResource;
}
