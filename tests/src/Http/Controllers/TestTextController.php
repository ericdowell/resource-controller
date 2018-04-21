<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Http\Controllers;

use EricDowell\ResourceController\Tests\Models\TestText;
use EricDowell\ResourceController\Http\Controllers\ModelMorphController;

class TestTextController extends ModelMorphController
{
    /**
     * @var string
     */
    protected $morphModel = TestText::class;
}
