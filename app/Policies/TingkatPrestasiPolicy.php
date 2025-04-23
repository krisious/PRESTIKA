<?php

namespace App\Policies;

use App\Models\User;
use App\Models\TingkatPrestasi;
use Illuminate\Auth\Access\HandlesAuthorization;

class TingkatPrestasiPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_tingkat::prestasi');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TingkatPrestasi $tingkatPrestasi): bool
    {
        return $user->can('view_tingkat::prestasi');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_tingkat::prestasi');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TingkatPrestasi $tingkatPrestasi): bool
    {
        return $user->can('update_tingkat::prestasi');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TingkatPrestasi $tingkatPrestasi): bool
    {
        return $user->can('delete_tingkat::prestasi');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_tingkat::prestasi');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, TingkatPrestasi $tingkatPrestasi): bool
    {
        return $user->can('force_delete_tingkat::prestasi');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_tingkat::prestasi');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, TingkatPrestasi $tingkatPrestasi): bool
    {
        return $user->can('restore_tingkat::prestasi');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_tingkat::prestasi');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, TingkatPrestasi $tingkatPrestasi): bool
    {
        return $user->can('replicate_tingkat::prestasi');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_tingkat::prestasi');
    }
}
