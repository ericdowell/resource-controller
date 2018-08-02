<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Controllers;

use EricDowell\ResourceController\Traits\With\ModelResource;

abstract class ResourceModelController extends Controller
{
    use ModelResource;
}
