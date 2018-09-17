<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use JD\Cloudder\Facades\Cloudder;

class Book extends Model
{
    //
    protected $fillable = [
        'title', 'user_id', 'file_path', 'description'
    ];

    protected $dates = ['deleted_at'];

    public function user(){
        return $this->belongsTo('App\User');
    }

    public function ratings(){
        return $this->hasMany("App\Models\Rating");
    }

    public function comments(){
        return $this->hasMany("App\Models\Comment");
    }

    public function getFilePathAttribute(){
        return Cloudder::secureShow($this->file_path);//get the entire secure path for the requested resource.
    }

    public function getAverageStars(){
        $stars = $this->ratings()->sum('stars');
        $count = $this->ratings()->count();
        if ($count == 0){
            return 0; //Return 0 to prevent division by zero error.
        }else {
            return round(($stars/$count), 1);//Rounds the figure to a decimal places
        }
    }
}
