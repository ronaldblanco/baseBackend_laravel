<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Transformers\PermissionTransformer;

class RoleTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
        'permissions'
    ];

    public function includePermissions(Role $role) {
      return $this->collection($role->permissions, new PermissionTransformer());
     }


    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $role)
    {
      return [
          'id' => $role->id,
          'name' => $role->name

      ];
    }
}
