<?php

namespace Tests\RestfulFilter\Database\Model;

use Kemodev\RestfulFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model {
    use Filterable;

    protected $table = 'posts';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $fillable = [
        'title',
        'description',
        'content',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $filterableColumns = [
        'title' => 'title',
        'description' => 'description',
        'authors.email' => 'authors.email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    protected $sortableColumns = [
        
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at'
    ];

    public function authors()
    {
        return $this->belongsToMany(
            User::class,
            'user_posts',
            'post_id',
            'user_id'
        );
    }
}