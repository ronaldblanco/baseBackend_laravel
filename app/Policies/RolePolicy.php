<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Http\Controllers\CompanyController;
use Illuminate\Auth\Access\Response;

use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->deny_message = 'You does not have the permission ';
    }
    public function store(User $user, $role)
    {
        $permission = 'role_create';
        return $user->can($permission) ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function give_permissions(User $user, $role)
    {
        $permission = 'role_edit';
        return $user->can($permission) and $user->company_id == $role->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function sync_permissions(User $user, $role)
    {
        $permission = 'role_edit';
        return $user->can($permission) and $user->company_id == $role->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function revoke_permissions(User $user, $role)
    {
        $permission = 'role_edit';
        return $user->can($permission) and $user->company_id == $role->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

}
