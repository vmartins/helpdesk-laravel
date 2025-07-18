<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\SocialiteUser;
use App\Models\User;

class SocialiteUserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->checkPermissionTo('view-any SocialiteUser');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SocialiteUser $socialiteuser): bool
    {
        return $user->checkPermissionTo('view SocialiteUser');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->checkPermissionTo('create SocialiteUser');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SocialiteUser $socialiteuser): bool
    {
        return $user->checkPermissionTo('update SocialiteUser');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SocialiteUser $socialiteuser): bool
    {
        return $user->checkPermissionTo('delete SocialiteUser');
    }

    /**
     * Determine whether the user can delete any models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->checkPermissionTo('delete-any SocialiteUser');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SocialiteUser $socialiteuser): bool
    {
        return $user->checkPermissionTo('restore SocialiteUser');
    }

    /**
     * Determine whether the user can restore any models.
     */
    public function restoreAny(User $user): bool
    {
        return $user->checkPermissionTo('restore-any SocialiteUser');
    }

    /**
     * Determine whether the user can replicate the model.
     */
    public function replicate(User $user, SocialiteUser $socialiteuser): bool
    {
        return $user->checkPermissionTo('replicate SocialiteUser');
    }

    /**
     * Determine whether the user can reorder the models.
     */
    public function reorder(User $user): bool
    {
        return $user->checkPermissionTo('reorder SocialiteUser');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SocialiteUser $socialiteuser): bool
    {
        return $user->checkPermissionTo('force-delete SocialiteUser');
    }

    /**
     * Determine whether the user can permanently delete any models.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->checkPermissionTo('force-delete-any SocialiteUser');
    }
}
