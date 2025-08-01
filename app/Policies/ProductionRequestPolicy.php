<?php

namespace App\Policies;

use App\Models\ProductionRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ProductionRequestPolicy
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
        $isautorise= $user->role->havePermission('read_production');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductionRequest  $productionRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, ProductionRequest $productionRequest)
    {
        $isautorise= $user->role->havePermission('read_production');
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
        $isautorise= $user->role->havePermission('create_production');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductionRequest  $productionRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, ProductionRequest $productionRequest)
    {
        $isautorise= $user->role->havePermission('edit_production');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductionRequest  $productionRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, ProductionRequest $productionRequest)
    {
        $isautorise= $user->role->havePermission('delete_production');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductionRequest  $productionRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, ProductionRequest $productionRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\ProductionRequest  $productionRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, ProductionRequest $productionRequest)
    {
        //
    }
} 