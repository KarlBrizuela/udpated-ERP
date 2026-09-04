<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'division',
        'permission_level',
        'access_scope',
        'permissions',
        'description',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the users that have this role (mapped by name string).
     */
    public function users()
    {
        // Match users by the 'position' column
        return $this->hasMany(User::class, 'position', 'name');
    }
}
