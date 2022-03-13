<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;
use App\Transformers\CrmMultiFieldTransformer;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class UserTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'roles', 'permissions', 'company','office'
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'fname','lname', 'email', 'password'
    ];

    public function includeRoles(User $user)
    {
        return $this->collection($user->roles, new RoleTransformer());
    }
    public function includePermissions(User $user)
    {
        return $this->collection($user->getAllPermissions(), new PermissionTransformer());
    }
    public function includeCompany(User $user)
    {
        return $this->item($user->company, new CompanyTransformer());
    }

    public function includeOffice(User $user)
    {
        return $this->item($user->office, new OfficeTransformer());
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'fname' => $user->fname,
            'lname' => $user->lname,
            'email' => $user->email,
            'password' => $user->password,
            'active' => $user->active,
            'office_id' => $user->office_id, 
            'company_id' => $user->company_id, 
       ];
    }
}
