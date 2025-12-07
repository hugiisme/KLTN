<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityCategory extends Model
{
    protected $table = 'activity_categories';
    protected $fillable = ['name', 'is_multi_level'];
    public $timestamps = false;
}
