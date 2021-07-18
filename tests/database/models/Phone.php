<?php

namespace Tests\RestfulFilter\Database\Model;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Phone extends Model {
    use Filterable;

    protected $table = 'phones';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'number',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $filterableColumns = [
        'user.email' => 'user.email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    protected $sortableColumns = [
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}