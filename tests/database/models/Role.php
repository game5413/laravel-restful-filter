<?php

namespace Tests\RestfulFilter\Database\Model;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    use Filterable;

    protected $table = 'roles';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'name',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $filterableColumns = [
        'name' => 'name',
        'search' => 'users.name,users.email',
        'users.email' => 'users.email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    protected $sortableColumns = [
        'name' => 'name',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}