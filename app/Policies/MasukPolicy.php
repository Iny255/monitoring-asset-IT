<?php

namespace App\Policies;

use App\Models\Masuk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MasukPolicy
{
  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return in_array($user->role, ['petugas', 'manager', 'super_admin']);
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, Masuk $masuk): bool
  {
    // super admin bebas
    if ($user->role === 'super_admin') {
      return true;
    }

    // selain itu harus sesuai perusahaan
    return $masuk->perusahaan_id == $user->perusahaan_id;
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    return in_array($user->role, ['petugas', 'super_admin']);
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, Masuk $masuk): bool
  {
    if ($user->role === 'super_admin') {
      return true;
    }

    return $user->role === 'petugas' && $masuk->perusahaan_id == $user->perusahaan_id;
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, Masuk $masuk): bool
  {
    if ($user->role === 'super_admin') {
      return true;
    }

    return $user->role === 'petugas' && $masuk->perusahaan_id == $user->perusahaan_id;
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, Masuk $masuk): bool
  {
    return false;
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, Masuk $masuk): bool
  {
    return false;
  }
}
