<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shorturl extends Model
{
    protected $table = 'shorturls';

    protected $fillable = ['long_url','short_url','count'];
}