<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionContent extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'name',
        'course_section_id', 
        'content',
    ];

    public function courseSection(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }
}
