<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = ['short_url','uv_cookie','ip','isp','country','province','city','browser','os'];
}
