<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'can_manage_settings',
        'is_system',
    ];

    protected $casts = [
        'can_manage_settings' => 'boolean',
        'is_system' => 'boolean',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'role_module', 'role_id', 'module_id')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'name');
    }

    public function hasModule(string $moduleCode): bool
    {
        if ($this->name === 'super_admin') {
            return true;
        }

        return $this->modules()->where('code', $moduleCode)->where('is_active', true)->exists();
    }
}
