<?php declare(strict_types=1);

use Faker\Generator as Faker;
use EricDowell\ResourceController\Tests\Models\TestUser;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(TestUser::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
    ];
});
