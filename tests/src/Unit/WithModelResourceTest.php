<?php

namespace EricDowell\ResourceController\Tests\Unit;

use EricDowell\ResourceController\Tests\TestCase;
use EricDowell\ResourceController\Tests\Models\TestUser;
use EricDowell\ResourceController\Http\Controllers\ResourceModelController;

class WithModelResourceTest extends TestCase
{
    /**
     * @test
     * @group unit
     * @group model-resource-trait
     */
    public function testGenerateDefaultsReturnsEmpty()
    {
        $noRouteAvailable = app(NoRouteAvailable::class);

        $this->assertEmpty($noRouteAvailable->getMergeData());
    }

    /**
     * @test
     * @group unit
     * @group model-resource-trait
     *
     * @expectedException \EricDowell\ResourceController\Exceptions\ModelClassCheckException
     * @expectedExceptionMessage Model property is empty.
     */
    public function testExceptionThrownClassPropertyEmpty()
    {
        app(NoModelClassProperty::class);
    }

    /**
     * @test
     * @group unit
     * @group model-resource-trait
     *
     * @expectedException \EricDowell\ResourceController\Exceptions\ModelClassCheckException
     * @expectedExceptionMessage Model [FakeRandomClassNameDoesNotExist] does not exist.
     */
    public function testExceptionThrowClassDoesNotExist()
    {
        app(NoModelClassDoesNotExist::class);
    }
}

class NoModelClassDoesNotExist extends ResourceModelController
{
    /**
     * @var string
     */
    protected $modelClass = 'FakeRandomClassNameDoesNotExist';
}

class NoModelClassProperty extends ResourceModelController
{
}

class NoRouteAvailable extends ResourceModelController
{
    /**
     * @var string
     */
    protected $modelClass = TestUser::class;

    /**
     * @return array
     */
    public function getMergeData()
    {
        return $this->mergeData;
    }
}
