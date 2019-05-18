<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use ResourceController\Tests\Models\TestPost;
use ResourceController\Tests\Models\TestUser;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(TestPost::class, function (Faker $faker) {
    return [
        'title' => $faker->words(3, true),
        'body' => $faker->paragraphs(3, true),
        'test_user_id' => function () {
            return data_get(auth()->user(), 'id') ?? factory(TestUser::class)->create()->id;
        },
    ];
});