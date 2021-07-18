<?php

namespace Tests\RestfulFilter\Database\Model;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class User extends Model {
    use Filterable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'role_id',
        'name',
        'age',
        'email',
        'password'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $filterableColumns = [
        'role.name' => 'role.name',
        'phone.number' => 'phone.number',
        'name' => 'name',
        'age' => 'age',
        'email' => 'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    protected $sortableColumns = [
        'name' => 'name',
        'age' => 'age',
        'email' => 'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function phone()
    {
        return $this->hasOne(Phone::class);
    }
}