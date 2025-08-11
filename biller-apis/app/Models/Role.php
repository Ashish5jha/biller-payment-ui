<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
    ];

    // Many-to-many relationship with Permissions
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }
}