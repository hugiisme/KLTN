<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgType extends Model
{
    protected $table = 'org_types';

    protected $fillable = [
        'name',
        'is_exclusive',
    ];

    public $timestamps = false;

    protected $casts = [
        'is_exclusive' => 'boolean',
    ];

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'org_type_id');
    }
}
