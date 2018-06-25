<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Unit;

use Mockery as m;
use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Console\Tester\CommandTester;
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
        $this->assertNull(TestUser::whereEmail($email)->first(), 'User model exists when it should NOT.');

        /** @var RegisterUser|\Mockery\MockInterface $command */
        $command = m::mock('\\'.RegisterUser::class.'[ask]');

        $command->shouldReceive('ask')->once()->with('Enter name')->andReturn($name);
        $command->shouldReceive('ask')->once()->with('Enter in an email')->andReturn($email);
        $command->shouldNotReceive('confirm')->never();
        $command->shouldReceive('secret')->once()->with('Enter password', null)->andReturn($password);
        $command->shouldReceive('secret')->once()->with('Confirm password')->andReturn($password);

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
}
