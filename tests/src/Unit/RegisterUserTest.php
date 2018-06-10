<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Unit;

use Mockery as m;
use Illuminate\Support\Facades\Hash;
use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Tests\Models\TestUser;
use EricDowell\ResourceController\Console\Commands\RegisterUser;

class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @group unit
     * @group register-user
     */
    public function testRegisterUserWithPassingModel()
    {
        $name = 'Tester';
        $email = 'hi@example.com';
        $password = 'secret';
        $command = m::mock(RegisterUser::class.'[ask]');

        $this->assertNull(TestUser::whereEmail($email)->first(), 'User model exists when it should NOT.');

        $command->shouldReceive('ask')->with('Enter name')->once()->andReturn($name);
        $command->shouldReceive('ask')->with('Enter in an email')->once()->andReturn($email);
        $command->shouldReceive('confirm')->never();
        $command->shouldReceive('secret')->with('Enter password')->once()->andReturn($password);
        $command->shouldReceive('secret')->with('Confirm password')->once()->andReturn($password);

        $this->addCommand($command);

        $this->artisan('register:user', ['--model' => TestUser::class]);

        $this->assertOutputContains('hi@example.com');
        $this->assertOutputDoesNotContains($password);

        $user = TestUser::whereEmail($email)->first();

        $this->assertInstanceOf(TestUser::class, $user);
        //$message = sprintf('Password \'%s\' doesn\'t pass hash check with user password hash %s.', $password, $user->password);
        //$this->assertTrue(Hash::check($password, $user->password), $message);
        $this->assertSame($name, $user->name);
    }

    /**
     * Call artisan command and return code.
     *
     * @param  string  $command
     * @param  array  $parameters
     *
     * @return int
     */
    public function artisan($command, $parameters = [])
    {
        return parent::artisan($command, array_merge($parameters, ['--no-interaction' => true]));
    }
}
