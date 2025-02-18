<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Category extends Model
{
    //
    use SoftDeletes;
    
    protected $fillable = [
        'name', // Web Design category name
        'slug', // slug = web-design
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Category has many courses
    // ORM explanation: Category has many courses
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }
}
