<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Http\Controllers\CompanyController;
use Illuminate\Auth\Access\Response;

class UserPolicy
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

    public function index(User $user)
    {
        $permission = 'users_list';
        return $user->can($permission) and $user->hasRole(['super-admin']) ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function show(User $user, $usertomanage) //for the admin to see profile of user
    {
        $permission = 'user_read';

        return $user->can($permission) && $user->company_id === $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function store(User $user, int $company_id)
    {
        $permission = 'user_create';
        if(intval($user->company_id) != intval($company_id)) return Response::deny($this->deny_message.$permission.' or company not allowed!');
        return $user->can($permission) ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
        //return ($user->can($permission) and $user->hasRole(['super-admin', 'admin'])) ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function import_store(User $user, $usertomanage)
    {
        $permission = 'users_import';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function update(User $user, $usertomanage)
    {
        $permission = 'user_edit';
        $permission1 = 'user_salesGroupManager';
        $permission2 = 'contacts_teamleader';
        return ($user->can($permission) || $user->can($permission1) || $user->can($permission2)) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function update_profile(User $user, $usertomanage)
    {
        return $user->id == $usertomanage->id ? Response::allow() : Response::deny($this->deny_message.'!');
    }

    public function destroy(User $user, $usertomanage)
    {
        $permission = 'user_delete';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function give_role(User $user, $usertomanage)
    {
        $permission = 'user_edit';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function sync_roles(User $user, $usertomanage)
    {
        $permission = 'user_edit';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function revoke_role(User $user, $usertomanage)
    {
        $permission = 'user_edit';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function show_user_roles(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function show_user_permissions(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function show_user_task(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function show_user_observer_tasks(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function show_user_responsible_tasks(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->company_id == $usertomanage->company_id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }

    public function show_user_my_tasks(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->id == $usertomanage->id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }
    public function show_user_products(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->id == $usertomanage->id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }
    public function store_user_products(User $user, $usertomanage)
    {
        $permission = 'user_edit';
        return $user->can($permission) and $user->id == $usertomanage->id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }
    public function delete_user_product(User $user, $usertomanage)
    {
        $permission = 'user_edit';
        return $user->can($permission) and $user->id == $usertomanage->id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }
    public function index_user_logs(User $user, $usertomanage)
    {
        $permission = 'user_edit';
        return $user->can($permission) and $user->id == $usertomanage->id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }
    public function group_count_schedule(User $user, $usertomanage)
    {
        $permission = 'user_read';
        return $user->can($permission) and $user->id == $usertomanage->id ? Response::allow() : Response::deny($this->deny_message.$permission.'!');
    }
    
}
