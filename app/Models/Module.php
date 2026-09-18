<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'group',
        'url',
        'route_name',
        'icon',
        'is_active',
        'order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_module', 'module_id', 'role_id')
            ->withTimestamps();
    }
}
