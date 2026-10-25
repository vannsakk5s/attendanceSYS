<?php

namespace App\Models;

use Database\Factories\TeacherFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class Teacher extends Authenticatable
{
    /** @use HasFactory<TeacherFactory> */
    use HasFactory, Notifiable;

    protected $table = 'teachers';

    protected $fillable = [
        'name',
        'email',
        'password_hash',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'telegram_username',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password_hash' => 'hashed',
        ];
    }

    /**
     * Get the password for the user.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash ?? '';
    }

    /**
     * Get the name of the password attribute for the user.
     */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    /**
     * Provide seamless compatibility with standard 'password' field assignments.
     */
    public function setPasswordAttribute($value): void
    {
        $this->attributes['password_hash'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * A teacher owns many classes.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'teacher_id');
    }
}
