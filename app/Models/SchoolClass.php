<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'teacher_id',
        'class_name',
        'subject_name',
        'share_token',
    ];

    protected static function booted(): void
    {
        static::creating(function ($class) {
            if (empty($class->share_token)) {
                $class->share_token = Str::random(32);
            }
        });
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ClassSchedule::class, 'class_id');
    }

    public function classSchedules(): HasMany
    {
        return $this->schedules();
    }

    public function attendanceSessions(): HasMany
    {
        return $this->hasMany(AttendanceSession::class, 'class_id');
    }

    public function getShareUrlAttribute(): ?string
    {
        if (empty($this->share_token)) {
            $token = Str::random(32);
            $this->update(['share_token' => $token]);
            $this->share_token = $token;
        }

        return route('public.class.attendance', $this->share_token);
    }
}
