<?php

declare(strict_types=1);

namespace EricDowell\ResourceController\Commands;

use RuntimeException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use EricDowell\ResourceController\Traits\UserResource;

class RegisterUser extends Command
{
    use UserResource;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:user
                            {--M|model= : Optional, exact user classname, must extend Eloquent Model class}
                            {--F|file= : Optional, exact path to json file containing user information}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register a new user account.';

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \RuntimeException
     */
    public function handle(): int
    {
        $userInstance = $this->getUserInstance();
        $attributes = $this->getUserAttributes($userInstance);

        $created = $userInstance->create($attributes);
        if ($created) {
            $outputSafe = array_except($attributes, ['password']);
            $this->info('User successfully created!');
            $this->table(array_keys($outputSafe), [array_values($outputSafe)]);

            return 0;
        }
        $this->info('Sorry, user was NOT created. Please try again.');

        return 1;
    }

    /**
     * Get all the attributes to create the User model with.
     *
     * @param \Illuminate\Database\Eloquent\Model $userInstance
     *
     * @return array
     */
    protected function getUserAttributes(Model $userInstance): array
    {
        $attributes = $this->getAttributesFromFile();
        if (! empty($attributes)) {
            return $attributes;
        }
        $attributes['name'] = $this->ask('Enter name');

        $this->askForEmail($attributes);

        $isUsernameFillable = in_array('username', $userInstance->getFillable()) || $userInstance->isUnguarded();

        $inputUsername = $isUsernameFillable ? $this->confirm('Do you want to set a username?') : false;
        if ($inputUsername) {
            $attributes['username'] = $this->ask('Enter in username');
        }

        $this->askForPassword($attributes);

        return $attributes;
    }

    /**
     * Return contents of json file if option is present.
     *
     * @return array
     * @throws \RuntimeException
     */
    protected function getAttributesFromFile(): array
    {
        $file = $this->option('file');
        if (! $file) {
            return [];
        } elseif (! File::exists($file)) {
            throw new RuntimeException("File: [{$file}] does NOT exist.");
        }
        $contents = File::get($file);

        return json_decode($contents, true);
    }

    /**
     * Make sure email is valid, set email as part of attributes.
     *
     * @param array $attributes
     *
     * @return void
     */
    protected function askForEmail(array &$attributes)
    {
        $email = $this->ask('Enter in an email');
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $attributes['email'] = $email;

            return;
        }
        $this->info("Email: '{$email}' is NOT valid, please try again.'");

        $this->askForEmail($attributes);
    }

    /**
     * Make sure password and confirmation password match, set hashed password as part of attributes.
     *
     * @param array $attributes
     *
     * @return void
     */
    protected function askForPassword(array &$attributes)
    {
        $password = $this->secret('Enter password');
        if (is_null($password) || empty(trim($password))) {
            $this->info('The password can NOT be empty, please try again.');
            $this->askForPassword($attributes);

            return;
        }
        $confirm = $this->secret('Confirm password');
        if ($password === $confirm) {
            $attributes['password'] = Hash::make($password);

            return;
        }
        $this->info('The password and confirmation password do NOT match, please try again.');

        $this->askForPassword($attributes);
    }
}
