<?php
namespace App\Domains\User\Permissions\Policies;

use App\Domains\User\Model\User;
use App\Domains\User\Permissions\Model\Permission;

class PermissionPolicy
{
    public function edit(User $user, Permission $permission)
    {
        // Add your authorization logic here
        return true; // Temporary, replace with actual logic
    }

    public function delete(User $user, Permission $permission)
    {
        // Add your authorization logic here
        return true; // Temporary, replace with actual logic
    }
}