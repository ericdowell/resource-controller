<?php

namespace EricDowell\ResourceController\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
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
     */
    public function testAutoFindModelClassWhenClassPropertyEmpty()
    {
        /** @var NoModelClassPropertyController $noModelClassProperty */
        $noModelClassProperty = app(NoModelClassPropertyController::class);

        $this->assertSame(NoModelClassProperty::class, $noModelClassProperty->getModelClass());
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

class NoModelClassProperty extends Model
{
}

class NoModelClassPropertyController extends ResourceModelController
{
    /**
     * @var string
     */
    protected $modelClassNamespace = 'EricDowell\ResourceController\Tests\Unit';

    /**
     * @return string
     */
    public function getModelClass()
    {
        return $this->modelClass();
    }
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
