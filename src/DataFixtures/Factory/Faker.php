<?php

namespace App\DataFixtures\Factory;

use Faker\Factory;
use Faker\Generator;

trait Faker
{
    private static ?Generator $faker = null;

    protected static function getFaker(): Generator
    {
        return static::$faker = static::$faker ?? Factory::create();
    }
}
