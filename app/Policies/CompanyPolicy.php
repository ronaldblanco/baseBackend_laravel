<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Http\Controllers\CompanyController;
use Illuminate\Auth\Access\Response;

class CompanyPolicy
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
        //dd("police");
        //die;
        $this->deny_message = 'You does not have the permission ';
    }

    public function index(User $user)
    {
        $permission = 'companys_list';
        return $user->can($permission) ? Response::allow() : Response::deny($this->deny_message . $permission . '!');
    }

    public function show(User $user, $company)
    {
        $permission = 'company_read';
        return $user->can($permission) and $user->company_id == $company->id ? Response::allow() : Response::deny($this->deny_message . $permission . '!');
    }

    public function store(User $user)
    {
        $permission = 'company_create';
        return $user->can($permission) ? Response::allow() : Response::deny($this->deny_message . $permission . '!');
    }

    public function update(User $user, $company)
    {
        $permission = 'company_edit';
        return $user->can($permission) and $user->company_id == $company->id ? Response::allow() : Response::deny($this->deny_message . $permission . '!');
    }

    public function destroy(User $user)
    {
        $permission = 'company_delete';
        return $user->can($permission) ? Response::allow() : Response::deny($this->deny_message . $permission . '!');
    }

}