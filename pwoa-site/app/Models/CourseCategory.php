<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function courses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Course::class);
    }
}
