<?php

namespace App\Policies;

use App\Models\Inventory;
use App\Models\User;

class InventoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role->havePermission('read_inventory');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Inventory $inventory): bool
    {
        return $user->role->havePermission('read_inventory');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role->havePermission('create_inventory');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Inventory $inventory): bool
    {
        return $user->role->havePermission('edit_inventory');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Inventory $inventory): bool
    {
        return $user->role->havePermission('delete_inventory');
    }

    /**
     * Determine whether the user can adjust stock.
     */
    public function adjustStock(User $user, Inventory $inventory): bool
    {
        return $user->role->havePermission('edit_inventory');
    }
} 