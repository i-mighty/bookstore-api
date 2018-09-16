<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model{
    protected $fillable = [
        'book_id', 'user_id', 'content'
    ];

    public function book(){
        return $this->belongsTo("App\Models\Book");
    }

    public function user(){
        return $this->belongsTo("App\User");
    }
}