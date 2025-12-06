<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'description',
        'parent_org_id',
        'org_type_id',
        'org_level_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_org_id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_org_id');
    }

    public function type()
    {
        return $this->belongsTo(OrgType::class, 'org_type_id');
    }

    public function level()
    {
        return $this->belongsTo(OrgLevel::class, 'org_level_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_orgs', 'org_id', 'user_id');
    }

    public function joinRequests()
    {
        return $this->hasMany(OrgJoinRequest::class, 'org_id');
    }
}
