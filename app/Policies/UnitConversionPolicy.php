<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\UnitConversion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UnitConversionPolicy
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
        $isautorise = $user->role->havePermission('read_unit_conversion');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitConversion  $unitConversion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, UnitConversion $unitConversion)
    {
        $isautorise = $user->role->havePermission('read_unit_conversion');
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
        $isautorise = $user->role->havePermission('create_unit_conversion');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitConversion  $unitConversion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, UnitConversion $unitConversion)
    {
        $isautorise = $user->role->havePermission('edit_unit_conversion');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitConversion  $unitConversion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, UnitConversion $unitConversion)
    {
        $isautorise = $user->role->havePermission('delete_unit_conversion');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitConversion  $unitConversion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, UnitConversion $unitConversion)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UnitConversion  $unitConversion
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, UnitConversion $unitConversion)
    {
        //
    }
} 