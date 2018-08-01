<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Tests\Unit;

use Illuminate\Support\Facades\Hash;
use EricDowell\ResourceController\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use EricDowell\ResourceController\Commands\RegisterUser;
use EricDowell\ResourceController\Tests\Models\TestUser;

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
        $this->assertNull(TestUser::whereEmail(TestRegisterUser::EMAIL)->first(), 'User model exists when it should NOT.');

        $this->addCommand(new TestRegisterUser());

        $this->artisan('register:user', ['--model' => TestUser::class]);

        $this->assertOutputContains(TestRegisterUser::EMAIL, sprintf('Email "%s" not found in output.', TestRegisterUser::EMAIL));
        $this->assertOutputDoesNotContains(TestRegisterUser::PASSWORD, 'Password was found as part of the output!');

        $user = TestUser::whereEmail(TestRegisterUser::EMAIL)->first();
        $this->assertInstanceOf(TestUser::class, $user);
        $this->assertSame(TestRegisterUser::NAME, $user->name);

        $message = sprintf('Password \'%s\' doesn\'t pass hash check with user password hash %s.', TestRegisterUser::PASSWORD, $user->password);
        $this->assertTrue(Hash::check(TestRegisterUser::PASSWORD, $user->password), $message);
    }
}

class TestRegisterUser extends RegisterUser
{
    /**
     * @var string
     */
    const NAME = 'Tester';

    /**
     * @var string
     */
    const EMAIL = 'hi@example.com';

    /**
     * @var string
     */
    const PASSWORD = 'secret';

    /**
     * Prompt the user for input.
     *
     * @param  string $question
     * @param  string|null $default
     * @return string
     */
    public function ask($question, $default = null)
    {
        switch ($question) {
            case 'Enter name':
                return self::NAME;
            case 'Enter in an email':
                return self::EMAIL;
            default:
                return;
        }
    }

    /**
     * Prompt the user for input but hide the answer from the console.
     *
     * @param  string $question
     * @param  bool $fallback
     * @return string
     */
    public function secret($question, $fallback = true)
    {
        switch ($question) {
            case 'Enter password':
            case 'Confirm password':
                return self::PASSWORD;
            default:
                return;
        }
    }
}
