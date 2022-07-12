<?php

namespace App\Policies;

use App\Models\ImportingRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ImportingRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        $isautorise= $user->role->havePermission('read_importing');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ImportingRequest $importingRequest)
    {
        $isautorise= $user->role->havePermission('read_importing');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $isautorise= $user->role->havePermission('create_importing');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ImportingRequest $importingRequest)
    {
        $isautorise= $user->role->havePermission('edit_importing');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ImportingRequest $importingRequest)
    {
        $isautorise= $user->role->havePermission('delete_importing');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ImportingRequest $importingRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ImportingRequest  $importingRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ImportingRequest $importingRequest)
    {
        //
    }
}
