<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */

  const ROLE_PETUGAS = 'petugas';

  protected $fillable = ['username', 'name', 'email', 'password', 'role', 'id_perusahaan'];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = ['password', 'remember_token'];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
    'email_verified_at' => 'datetime',
    'password' => 'hashed',
  ];

  public function scopePetugasOrParticipant(Builder $query)
  {
    return $query->whereIn('role', ['petugas']);
  }

  public function getDashboardUrl()
  {
    return match ($this->role) {
      'petugas' => '/dashboard/petugas',
      default => '/login',
    };
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }
  public function historyHakAkses()
  {
    return $this->hasMany(HistoryHakAkses::class, 'user_id');
  }
  public function historyMutasis()
  {
    return $this->hasMany(HistoryMutasi::class, 'created_by');
  }
  public function peminjamans()
  {
    return $this->hasMany(Peminjaman::class);
  }
}
