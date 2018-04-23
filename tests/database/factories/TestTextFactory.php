<?php

declare(strict_types=1);

use Faker\Generator as Faker;
use EricDowell\ResourceController\Tests\Models\TestPost;
use EricDowell\ResourceController\Tests\Models\TestText;
use EricDowell\ResourceController\Tests\Models\TestUser;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(TestPost::class, function (Faker $faker) {
    return [
        'title' => $faker->words(3, true),
        'body' => $faker->paragraph(),
        'user_id' => data_get(auth()->user(), 'id') ?? factory(TestUser::class)->create()->id,
    ];
});

$factory->define(TestText::class, function (Faker $faker) {
    /** @var \Illuminate\Database\Eloquent\Model $post */
    $post = factory(TestPost::class)->create();

    return [
        'text_id' => $post->id,
        'text_type' => str_singular($post->getTable()),
        'user_id' => $post->user_id,
    ];
}, TestPost::class);
