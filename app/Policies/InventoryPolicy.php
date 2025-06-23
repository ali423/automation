<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Inventory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class InventoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        $isautorise= $user->role->havePermission('read_inventory');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    public function view(User $user, Inventory $inventory)
    {
        $isautorise= $user->role->havePermission('read_inventory');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    public function create(User $user)
    {
        $isautorise= $user->role->havePermission('create_inventory');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    public function update(User $user, Inventory $inventory)
    {
        $isautorise= $user->role->havePermission('edit_inventory');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    public function delete(User $user, Inventory $inventory)
    {
        $isautorise= $user->role->havePermission('delete_inventory');
        return $isautorise
            ? Response::allow()
            : Response::deny("شما مجاز نیستید ");
    }

    public function restore(User $user, Inventory $inventory)
    {
        //
    }

    public function forceDelete(User $user, Inventory $inventory)
    {
        //
    }
} 