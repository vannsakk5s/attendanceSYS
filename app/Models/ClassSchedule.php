<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSchedule extends Model
{
    use HasFactory;

    protected $table = 'class_schedules';

    protected $fillable = [
        'class_id',
        'day_of_week',
        'shift',
        'start_time',
        'end_time',
    ];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function class(): BelongsTo
    {
        return $this->schoolClass();
    }
}
