<?php

namespace App\Policies;

use App\Models\Commodity;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CommodityPolicy
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
        $isautorise= $user->role->havePermission('read_commodity');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Commodity $commodity)
    {
        $isautorise= $user->role->havePermission('read_commodity');
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
        $isautorise= $user->role->havePermission('create_commodity');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Commodity $commodity)
    {
        $isautorise= $user->role->havePermission('edit_commodity');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Commodity $commodity)
    {
        $isautorise= $user->role->havePermission('delete_commodity');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Commodity $commodity)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Commodity  $commodity
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Commodity $commodity)
    {
        //
    }
}
