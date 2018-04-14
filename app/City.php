<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    public function coutries() {
        return $this->belongsToMany('App\Country')->withTimestamps();
    }

    public function profiles () {
        return $this->hasMany('App\Profile');

    }

    protected $fillable = [
        'city',
    ];
    
}
