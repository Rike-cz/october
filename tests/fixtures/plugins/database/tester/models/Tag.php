<?php namespace Database\Tester\Models;

use Model;

class Tag extends Model
{
    public $table = 'database_tester_tags';

    public $timestamps = false;

    public $morphedByMany = [
        'posts'  => ['Database\Tester\Models\Post', 'name' => 'taggable', 'table' => 'database_tester_taggables', 'pivot' => ['comment']],
    ];

    public $fillable = [
        'name',
    ];
}
