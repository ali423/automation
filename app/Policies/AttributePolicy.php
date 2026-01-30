<?php

namespace App\Policies;

use App\Models\Attribute;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AttributePolicy
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
        $isAuthorised = $user->role->havePermission('read_attribute');
        return $isAuthorised
            ? Response::allow()
            : Response::deny("شما مجاز نیستید");
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Attribute $attribute)
    {
        $isAuthorised = $user->role->havePermission('read_attribute');
        return $isAuthorised
            ? Response::allow()
            : Response::deny("شما مجاز نیستید");
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        $isAuthorised = $user->role->havePermission('create_attribute');
        return $isAuthorised
            ? Response::allow()
            : Response::deny("شما مجاز نیستید");
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Attribute $attribute)
    {
        $isAuthorised = $user->role->havePermission('edit_attribute');
        return $isAuthorised
            ? Response::allow()
            : Response::deny("شما مجاز نیستید");
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Attribute $attribute)
    {
        $isAuthorised = $user->role->havePermission('delete_attribute');
        return $isAuthorised
            ? Response::allow()
            : Response::deny("شما مجاز نیستید");
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Attribute $attribute)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Attribute  $attribute
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Attribute $attribute)
    {
        //
    }
}
