<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Course extends Model
{
    //
    use SoftDeletes;
    
    protected $fillable = [
        'slug', 
        'name',
        'thumbnail',
        'about',
        'is_popular',
        'category_id', 
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function benefits(): HasMany
    {
        return $this->hasMany(CourseBenefit::class);
    }

    public function courseSections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'course_id');
    }

    public function courseTestimonials(): HasMany
{
    return $this->hasMany(CourseTestimonial::class, 'course_id');
}

    public function courseStudents(): HasMany
    {
        return $this->hasMany(CourseStudent::class, 'course_id');
    }

    public function courseMentors(): HasMany
    {
        return $this->hasMany(CourseMentor::class, 'course_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getContentCountAttribute()
    {
        return $this->courseSections->sum(function ($section){
            return $section->sectionContents->count();
        });
    }
}
