<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrgLevel extends Model
{
    protected $table = 'org_levels';

    protected $fillable = [
        'equivalent_name',
        'level_index',
    ];

    public $timestamps = false;

    protected $casts = [
        'level_index' => 'integer',
    ];

    /**
     * Danh sách tổ chức thuộc cấp này
     */
    public function organizations()
    {
        return $this->hasMany(Organization::class, 'org_level_id');
    }
}
