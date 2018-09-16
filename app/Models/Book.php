<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    //
    protected $fillable = [
        'title', 'user_id', 'file_path', 'description'
    ];
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function ratings(){
        return $this->hasMany("App\Models\Rating");
    }
    public function comments(){
        return $this->hasMany("App\Models\Comment");
    }
}
