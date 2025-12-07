<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $fillable = [
        'title',
        'detail',
        'org_id',
        'semester_id',
        'academic_year_id',
        'creator_id',
        'parent_activity_id',
        'submission_requirement_id',
        'is_visible',
        'activity_type_id',
        'activity_category_id',
        'status',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function activityType()
    {
        return $this->belongsTo(ActivityType::class, 'activity_type_id');
    }

    public function activityCategory()
    {
        return $this->belongsTo(ActivityCategory::class, 'activity_category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'org_id');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function submissionRequirement()
    {
        return $this->belongsTo(SubmissionRequirement::class, 'submission_requirement_id');
    }

    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_activity_id');
    }

    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_activity_id');
    }
}
