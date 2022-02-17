<?php

namespace Database\Seeders;

use App\Models\User;

use App\Models\Office;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Reset cached roles and permissions
        //app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $company = Company::create(["name" => "Main", "email" => "company@company.com", "phone" => "1236547896"]);
        $office = Office::create(["name" => "Main", "email" => "office@office.com", "phone" => "1236547895", "company_id"=>$company->id]);

        // users
        Permission::create(['guard_name' => 'api', 'name' => 'user_read', 'module_name' => 'user']);
        Permission::create(['guard_name' => 'api', 'name' => 'users_list', 'module_name' => 'user']);
        Permission::create(['guard_name' => 'api', 'name' => 'user_create', 'module_name' => 'user']);
        Permission::create(['guard_name' => 'api', 'name' => 'user_edit', 'module_name' => 'user']);
        Permission::create(['guard_name' => 'api', 'name' => 'user_delete', 'module_name' => 'user']);
        Permission::create(['guard_name' => 'api', 'name' => 'users_import', 'module_name' => 'user']);
        $permissions_users = [
            'user_read',
            'users_list',
            'user_create',
            'user_edit',
            'user_delete',
            'users_import'
        ];
        $roleUser = Role::create(['guard_name' => 'api', 'name' => 'user_manage'])
            ->givePermissionTo($permissions_users);


        //companys
        Permission::create(['guard_name' => 'api', 'name' => 'company_read', 'module_name' => 'company']);
        Permission::create(['guard_name' => 'api', 'name' => 'companys_list', 'module_name' => 'company']);
        Permission::create(['guard_name' => 'api', 'name' => 'company_create', 'module_name' => 'company']);
        Permission::create(['guard_name' => 'api', 'name' => 'company_edit', 'module_name' => 'company']);
        Permission::create(['guard_name' => 'api', 'name' => 'company_delete', 'module_name' => 'company']);
        $permissions_companys = [
            'company_read',
            'companys_list',
            'company_create',
            'company_edit',
            'company_delete'
        ];
        $roleCompany = Role::create(['guard_name' => 'api', 'name' => 'company_manage'])
            ->givePermissionTo($permissions_companys);

        //offices
        Permission::create(['guard_name' => 'api', 'name' => 'office_read', 'module_name' => 'office']);
        Permission::create(['guard_name' => 'api', 'name' => 'offices_list', 'module_name' => 'office']);
        Permission::create(['guard_name' => 'api', 'name' => 'office_create', 'module_name' => 'office']);
        Permission::create(['guard_name' => 'api', 'name' => 'office_edit', 'module_name' => 'office']);
        Permission::create(['guard_name' => 'api', 'name' => 'office_delete', 'module_name' => 'office']);
        $permissions_offices = [
            'office_read',
            'offices_list',
            'office_create',
            'office_edit',
            'office_delete'
        ];
        $roleOffice = Role::create(['guard_name' => 'api', 'name' => 'office_manage'])
            ->givePermissionTo($permissions_offices);

        $user = User::create(["fname" => "Super", "email" => "super@super.com", "password" => bcrypt("123"), "company_id"=>$company->id,"office_id"=>$office->id]);

        $user->assignRole($roleUser);
        $user->assignRole($roleCompany);
        $user->assignRole($roleOffice);
    }
}
