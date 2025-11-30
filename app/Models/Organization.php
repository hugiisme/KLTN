<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'parent_org_id',
        'org_type_id',
        'org_level_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Tổ chức cha (self relation)
     */
    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_org_id');
    }

    /**
     * Danh sách tổ chức con
     */
    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_org_id');
    }

    /**
     * Quan hệ loại tổ chức
     */
    public function type()
    {
        return $this->belongsTo(OrgType::class, 'org_type_id');
    }

    /**
     * Quan hệ cấp tổ chức
     */
    public function level()
    {
        return $this->belongsTo(OrgLevel::class, 'org_level_id');
    }
}
