<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'description', 'thumbnail_path', 'is_published', 'course_category_id'];

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function quiz(): HasOne
    {
        return $this->hasOne(Quiz::class);
    }
    

    /**
     * Users enrolled in this course.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('joined_at', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Certificates issued for this course.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function quizResults(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(QuizResult::class, Quiz::class);
    }
}
