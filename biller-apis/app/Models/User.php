<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    // Default values
    protected $attributes = [
        'status' => true, // Default to active (1)
    ];

    // Relationship with Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Scope for active users
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    // Scope for inactive users
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }
}