<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseBenefit extends Model
{
    //
    use SoftDeletes;

    public $fillable = [
        'name',
        'course_id',
    ];
}
