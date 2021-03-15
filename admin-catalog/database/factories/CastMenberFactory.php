<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CastMenber;
use Faker\Generator as Faker;

$factory->define(CastMenber::class, function (Faker $faker) {
    return [
        'name' => $faker->lastName, 'type' => array_rand([CastMenber::TYPE_DIRECTOR, CastMenber::TYPE_ACTOR])
    ];
});
