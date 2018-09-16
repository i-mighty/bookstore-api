<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'book_id', 'stars', 'user_id'
    ];
    public function book(){
        return $this->belongsTo("App\Models\Book");
    }
    public function user(){
        return $this->belongsTo("App\User");
    }
}
