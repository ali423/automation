<?php

namespace App\Policies;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class SellerPolicy
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
        $isautorise= $user->role->havePermission('read_seller');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Seller $seller)
    {
        $isautorise= $user->role->havePermission('read_seller');
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
        $isautorise= $user->role->havePermission('create_seller');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Seller $seller)
    {
        $isautorise= $user->role->havePermission('edit_seller');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Seller $seller)
    {
        $isautorise= $user->role->havePermission('delete_seller');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Seller $seller)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Seller $seller)
    {
        //
    }
}
