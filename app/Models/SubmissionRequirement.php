<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionRequirement extends Model
{
    protected $table = 'submission_requirements';
    protected $fillable = [
        'name',
        'require_file_upload',
        'require_text_input',
        'allowed_file_types',
        'max_file_size_mb'
    ];
    public $timestamps = false;
}
