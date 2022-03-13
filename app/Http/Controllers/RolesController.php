<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Models\Organization;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Transformers\RoleTransformer;
use Spatie\Fractal\Fractal;

class RolesController extends Controller
{


  public function store(StoreRoleRequest $request)
  {
    $orgId = $request->get('organization_id');
    $organization = Organization::findOrFail($orgId);

    //$this->authorize('store', Role::class);

    $role_name = $request->get('name');

    $role = Role::create(['guard_name' => 'api', 'name' => $role_name, 'organization_id' => $orgId]);

    if ($request->has('permissions')) {
      $role->syncPermissions($request->permissions);
    }

    return Fractal::create()
      ->item($role)
      ->transformWith(new RoleTransformer)
      ->includePermissions()
      ->toArray();
  }

  public function update(Request $request, $id)
  {
    $role = Role::findOrFail($id);
    //$this->authorize('update', $role);

    if ($request->has('permissions')) {
      $role->syncPermissions($request->permissions);
    }

    $role->update($request->all());

    return Fractal::create()
      ->item($role)
      ->transformWith(new RoleTransformer)
      ->includePermissions()
      ->toArray();
  }

  public function give_permissions(Request $request, $id)
  {
    $permission_name = $request->get('name');
    $role = Role::findOrFail($id);
    $this->authorize('give_permissions', $role);
    $role->givePermissionTo($permission_name);

    //return $role;
    return Fractal::create()
      ->item($role)
      ->transformWith(new RoleTransformer)
      ->includePermissions()
      ->toArray();
  }

  public function sync_permissions(Request $request, $id)
  {
    $permissions = $request->get('permissions'); //array {'permission1','permission2'}
    $role = Role::findOrFail($id);
    $this->authorize('sync_permissions', $role);
    //dd($permissions,$role);
    $role->syncPermissions($permissions);

    //return $role;
    return Fractal::create()
      ->item($role)
      ->transformWith(new RoleTransformer)
      ->includePermissions()
      ->toArray();
  }

  public function revoke_permissions($id, $permissionId)
  {
    $role = Role::findOrFail($id);
    $this->authorize('revoke_permissions', $role);
    $permission = Permission::findOrFail($permissionId);
    $permission->removeRole($role);

    //return $role;
    return Fractal::create()
      ->item($role)
      ->transformWith(new RoleTransformer)
      ->includePermissions()
      ->toArray();
  }


  public function destroy($id)
  {
    $role = Role::findOrFail($id);
    //$this->authorize('destroy', $role);
    $role->delete();

    return response()->json([
      "message" => "role deleted"
    ], 202);
  }
}
