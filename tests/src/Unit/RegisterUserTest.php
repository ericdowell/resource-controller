<?php

declare(strict_types=1);

namespace ResourceController\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use ResourceController\Commands\RegisterUser;
use ResourceController\Tests\Models\TestUser;
use ResourceController\Tests\TestCase;

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
        $password = 'password';
        $this->assertNull(TestUser::whereEmail($email)->first(), 'User model exists when it should NOT.');

        $this->artisan(RegisterUser::class, ['--model' => TestUser::class])
             ->expectsQuestion('Enter name', $name)
             ->expectsQuestion('Enter in an email', $email)
             ->expectsQuestion('Enter password', $password)
             ->expectsQuestion('Confirm password', $password)
             ->assertExitCode(0);

        $user = TestUser::whereEmail($email)->first();
        $this->assertInstanceOf(TestUser::class, $user);
        $this->assertSame($name, $user->name);

        $message = sprintf('Password \'%s\' doesn\'t pass hash check with user password hash %s.', $password, $user->password);
        $this->assertTrue(Hash::check($password, $user->password), $message);
    }
}
