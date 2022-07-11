<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WithdrawalRequest;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class WithdrawalRequestPolicy
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
        $isautorise= $user->role->havePermission('read_withdrawal');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, WithdrawalRequest $withdrawalRequest)
    {
        $isautorise= $user->role->havePermission('read_withdrawal');
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
        $isautorise= $user->role->havePermission('create_withdrawal');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, WithdrawalRequest $withdrawalRequest)
    {
        $isautorise= $user->role->havePermission('edit_withdrawal');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, WithdrawalRequest $withdrawalRequest)
    {
        $isautorise= $user->role->havePermission('delete_withdrawal');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, WithdrawalRequest $withdrawalRequest)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\WithdrawalRequest  $withdrawalRequest
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, WithdrawalRequest $withdrawalRequest)
    {
        //
    }
}
