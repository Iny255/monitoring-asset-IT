<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    const ROLE_PETUGAS = 'petugas';
    const ROLE_BIDAN = 'bidan';
    const ROLE_ORTU = 'ortu';
    const ROLE_KADES = 'kades';

    protected $fillable = [
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

   public function scopePetugasOrParticipant($query)
    {
        return $query->whereIn('role', ['petugas', 'ortu', 'bidan', 'kades']);
    }

    public function getDashboardUrl()
{
    switch ($this->role) {
        case 'ortu':
            return '/dashboard/ortu';
        case 'petugas':
            return '/dashboard/petugas';
        case 'kades':
            return '/dashboard/kades';
        default:
            return '/dashboard';
    }
}
    
}