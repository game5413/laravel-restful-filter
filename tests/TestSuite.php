<?php

namespace Tests\RestfulFilter;

use PHPUnit\Framework\TestCase;
use Illuminate\Database\Capsule\Manager as Capsule;
use Tests\RestfulFilter\Database\Model\{User, Role, Post, Phone};

class TestSuite extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $faker = \Faker\Factory::create();

        $faker->seed((int) $_ENV['faker_seed']);

        $factory = new \Illuminate\Database\Eloquent\Factory($faker);

        Capsule::schema()->create('roles', function($table) {
            $table->unsignedInteger('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Capsule::schema()->create('users', function($table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('role_id');
            $table->string('name');
            $table->unsignedInteger('age');
            $table->string('email');
            $table->string('password');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
        });

        Capsule::schema()->create('phones', function($table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('user_id')->primary();
            $table->string('number');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        Capsule::schema()->create('posts', function($table) {
            $table->unsignedInteger('id')->primary();
            $table->string('title', 100);
            $table->string('description')->nullable();
            $table->mediumText('content');
            $table->timestamps();
        });

        Capsule::schema()->create('user_posts', function($table) {
            $table->unsignedInteger('user_id')->primary();
            $table->unsignedInteger('post_id')->primary();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('post_id')->references('id')->on('posts');
        });

        foreach(['Admin', 'Author'] as $role)
        {
            Role::create([
                'name' => $role
            ]);
        }

        $factory->define(User::class, function() use($faker) {
            return [
                'role_id' => $faker->numberBetween(1, 2),
                'name' => $faker->name(),
                'age' => $faker->numberBetween(16, 45),
                'email' => $faker->freeEmail(),
                'password' => $faker->password()
            ];
        });

        $factory->of(User::class)->times(10)->create();

        $factory->define(Phone::class, function() use($faker) {
            return [
                'user_id' => $faker->unique()->numberBetween(1, 10),
                'number' => $faker->e164PhoneNumber(),
            ];
        });

        $factory->of(Phone::class)->times(10)->create();

        //Reset counter unique
        $faker->unique(true);

        $factory->define(Post::class, function() use($faker) {
            return [
                'title' => $faker->text(100),
                'description' => $faker->text(),
                'content' => $faker->paragraphs(2, true),
            ];
        });

        $factory->of(Post::class)->times(1)->create();


        $post = Post::first();

        for($i = 0; $i < 10; $i++) {
            $post->authors()->attach([
                'user_id' => $faker->unique()->numberBetween(1, 10)
            ]);            
        }
    }

    public static function tearDownAfterClass(): void
    {
        Capsule::schema()->dropIfExists('roles');

        Capsule::schema()->dropIfExists('users');
        
        Capsule::schema()->dropIfExists('phones');

        Capsule::schema()->dropIfExists('posts');

        Capsule::schema()->dropIfExists('user_posts');
    }
}
