<?php declare(strict_types=1);

namespace Tests\RestfulFilter;

use Illuminate\Support\Collection;
use Tests\RestfulFilter\Database\Model\{User, Role, Post, Phone};

/**
 * @covers \Filtering
 * @uses \RestfulFilter
 */
final class FilterCase extends TestSuite
{
    public function testFilterEqual(): void
    {
        $filter = [
            'name' => 'Darrick Ward'
        ];

        $data = User::searchable($filter)->get();

        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        $this->assertCount(1, $data->toArray());

        $this->assertSame($filter['name'], $data->first()->name);
    }

    public function testFilterNotEqual(): void
    {
        $value = 'kaylee16@yahoo.com';

        $filter = [
            'email' => "not:$value"
        ];

        $data = User::searchable($filter)->get();
        
        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        foreach($data as $user) {
            $this->assertNotEquals($value, $user->email);
        }
    }

    public function testFilterLessThan(): void
    {
        $value = 32;

        $filter = [
            'age' => "lt:$value"
        ];

        $data = User::searchable($filter)->get();
        
        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        foreach($data as $user) {
            $this->assertLessThan($value, $user->age);
        }
    }

    public function testFilterLessThanEqual(): void
    {
        $value = 34;

        $filter = [
            'age' => "lte:$value"
        ];

        $data = User::searchable($filter)->get();
        
        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        foreach($data as $user) {
            $this->assertLessThanOrEqual($value, $user->age);
        }
    }

    public function testFilterGreaterThan(): void
    {
        $value = 24;

        $filter = [
            'age' => "gt:$value"
        ];

        $data = User::searchable($filter)->get();
        
        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        foreach($data as $user) {
            $this->assertGreaterThan($value, $user->age);
        }
    }

    public function testFilterGreaterThanEqual(): void
    {
        $value = 24;

        $filter = [
            'age' => "gte:$value"
        ];

        $data = User::searchable($filter)->get();
        
        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        foreach($data as $user) {
            $this->assertGreaterThanOrEqual($value, $user->age);
        }
    }

    public function testFilterLike(): void
    {
        $value = 'yahoo.com';

        $filter = [
            'email' => "like:$value"
        ];

        $data = User::searchable($filter)->get();

        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        foreach($data as $user) {
            $this->assertStringContainsString($value, $user->email);
        }
    }

    public function testFilterMultipleParams(): void
    {
        $filter = [
            'age' => 'gte:32',
            'email' => 'like:gmail.com'
        ];

        $data = User::searchable($filter)->get();

        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        foreach($data as $user) {
            $this->assertGreaterThanOrEqual(32, $user->age);
            $this->assertStringContainsString('gmail.com', $user->email);
        }
    }

    public function testFilterAllOfKind(): void
    {
        $data = Role::searchable([
            'search' => 'test'
        ])
            ->toSql();

        $this->assertSame(
            'select * from "roles" where (exists (select * from "users" where "roles"."id" = "users"."role_id" and "name" = ?) or exists (select * from "users" where "roles"."id" = "users"."role_id" and "email" = ?))',
            $data
        );
    }

    public function testFilterHasOne(): void
    {
        $filter = [
            'phone.number' => '+15403755085'
        ];

        $data = User::with('phone')
            ->searchable($filter)
            ->get();

        $this->assertInstanceOf(
            Collection::class,
            $data
        );

        $this->assertNotEmpty($data->toArray());

        $this->assertCount(1, $data->toArray());

        $this->assertSame($filter['phone.number'], $data->first()->phone->number);
    }

    public function testFilterHasMany(): void
    {
        $filter = [
            'users.email' => 'like:hotmail.com'
        ];

        $data = Role::with('users')
            ->searchable($filter)
            ->first();

        $this->assertInstanceOf(
            Role::class,
            $data
        );

        $this->assertNotNull($data);
    }

    public function testFilterBelongsTo(): void
    {
        $filter = [
            'users.email' => 'like:hotmail.com'
        ];

        $data = Phone::with('user')
            ->searchable($filter)
            ->first();

        $this->assertInstanceOf(
            Phone::class,
            $data
        );

        $this->assertNotNull($data);

        $this->assertNotNull($data->user);
    }

    public function testFilterBelongsToMany(): void
    {
        $filter = [
            'authors.email' => 'like:yahoo.com'
        ];

        $data = Post::with('authors')
            ->searchable($filter)
            ->first();

        $this->assertNotNull($data);
    }
}
