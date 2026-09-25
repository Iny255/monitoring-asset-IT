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

  protected $fillable = ['username', 'name', 'email', 'password', 'role', 'id_perusahaan', 'karyawan_id'];

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

  public function setRoleAttribute($value)
  {
    if (is_numeric($value)) {
      $role = Role::find((int) $value);
      $this->attributes['role'] = $role ? $role->name : (string) $value;
    } else {
      $this->attributes['role'] = $value;
    }
  }

  public function getRoleAttribute($value)
  {
    if (is_numeric($value)) {
      $role = Role::find((int) $value);
      return $role ? $role->name : (string) $value;
    }
    return $value;
  }

  public function getDashboardUrl()
  {
    $rawRole = (string) ($this->attributes['role'] ?? $this->role ?? '');
    $currentRole = is_numeric($rawRole) ? ($this->roleDefinition?->name ?? $rawRole) : $rawRole;
    $normalizedRole = strtolower(str_replace([' ', '-'], '_', trim($currentRole)));

    if (in_array($normalizedRole, ['super_admin', '1', 'superadmin']) || in_array($rawRole, ['1', 1])) {
      return '/dashboard/superadmin';
    }

    if ($normalizedRole === 'petugas' || $rawRole === '2' || in_array($normalizedRole, ['petugas_it_support', 'teknisi'])) {
      return '/dashboard/petugas';
    }

    // Cek role dinamis dari relasi roleDefinition
    $roleDef = $this->roleDefinition
      ?: (\App\Models\Role::where('name', $currentRole)->first()
        ?: (\App\Models\Role::whereRaw('LOWER(name) = ?', [strtolower($currentRole)])->first()
          ?: (is_numeric($rawRole) ? \App\Models\Role::find((int) $rawRole) : null)));

    if ($roleDef) {
      if ($roleDef->name === 'super_admin' || $roleDef->can_manage_settings) {
        return '/dashboard/superadmin';
      }

      // Cek apakah punya akses ke dashboard tertentu
      if ($roleDef->hasModule('dashboard_petugas') || $roleDef->modules()->where('is_active', true)->where('url', 'dashboard/petugas')->exists()) {
        return '/dashboard/petugas';
      }

      if ($roleDef->hasModule('dashboard_superadmin') || $roleDef->modules()->where('is_active', true)->where('url', 'dashboard/superadmin')->exists()) {
        return '/dashboard/superadmin';
      }

      if ($roleDef->hasModule('dashboard_aset_saya') || $roleDef->modules()->where('is_active', true)->where('url', 'dashboard/aset-saya')->exists()) {
        return '/dashboard/aset-saya';
      }

      $firstModule = $roleDef->modules()->where('is_active', true)->whereNotNull('url')->orderBy('order')->first();
      if ($firstModule && $firstModule->url) {
        return '/' . ltrim($firstModule->url, '/');
      }
    }

    if (in_array($normalizedRole, ['user', 'karyawan', '3', '4'])) {
      return '/dashboard/aset-saya';
    }

    return '/dashboard/aset-saya';
  }

  public function roleDefinition()
  {
    $roleVal = $this->attributes['role'] ?? $this->role ?? null;
    if (is_numeric($roleVal)) {
      return $this->belongsTo(Role::class, 'role', 'id');
    }
    return $this->belongsTo(Role::class, 'role', 'name');
  }

  public function canManageSettings(): bool
  {
    if ($this->role === 'super_admin' || $this->role === '1' || $this->role === 1) {
      return true;
    }
    $role = $this->roleDefinition;
    if (!$role && is_numeric($this->role)) {
      $role = Role::find((int) $this->role);
    }
    return (bool) ($role?->can_manage_settings ?? false);
  }

  public function hasModuleAccess(string $moduleCode): bool
  {
    if ($this->role === 'super_admin' || $this->role === '1' || $this->role === 1) {
      return true;
    }
    $role = $this->roleDefinition;
    if (!$role && is_numeric($this->role)) {
      $role = Role::find((int) $this->role);
    }
    if (!$role) {
      return false;
    }
    return $role->hasModule($moduleCode);
  }

  public function karyawan()
  {
    return $this->belongsTo(Karyawan::class, 'karyawan_id');
  }

  public function perusahaan()
  {
    return $this->belongsTo(Perusahaan::class, 'id_perusahaan');
  }

  public function getAccessibleCompanyIds()
  {
    if ($this->role === 'super_admin' || !$this->id_perusahaan) {
      return null;
    }
    return $this->perusahaan ? $this->perusahaan->getAllCompanyIds() : collect([$this->id_perusahaan]);
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
  public function tickets()
  {
    return $this->hasMany(Ticket::class, 'user_id');
  }
  public function assignedTickets()
  {
    return $this->hasMany(Ticket::class, 'assigned_to');
  }
}
