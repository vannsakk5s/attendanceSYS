<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $table = 'attendance_sessions';

    protected $fillable = [
        'class_id',
        'session_date',
        'start_time',
        'end_time',
        'share_token',
    ];

    protected static function booted(): void
    {
        static::creating(function ($session) {
            if (empty($session->share_token)) {
                $session->share_token = Str::random(32);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function class(): BelongsTo
    {
        return $this->schoolClass();
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'attendance_session_id');
    }

    public function getPresentCountAttribute(): int
    {
        return $this->attendanceRecords->where('status', 'present')->count();
    }

    public function getLateCountAttribute(): int
    {
        return $this->attendanceRecords->where('status', 'late')->count();
    }

    public function getAbsentCountAttribute(): int
    {
        return $this->attendanceRecords->where('status', 'absent')->count();
    }

    public function getExcusedCountAttribute(): int
    {
        return $this->attendanceRecords->where('status', 'excused')->count();
    }

    public function getAttendanceRateAttribute(): float
    {
        $total = $this->attendanceRecords->count();
        if ($total === 0) {
            return 0.0;
        }

        // Present counts as 100%, Late counts as 50% or present
        $attended = $this->present_count + $this->late_count;
        return round(($attended / $total) * 100, 1);
    }

    public function getShareUrlAttribute(): string
    {
        if (empty($this->share_token)) {
            $token = Str::random(32);
            $this->update(['share_token' => $token]);
            $this->share_token = $token;
        }

        return route('public.session.attendance', $this->share_token);
    }
}

