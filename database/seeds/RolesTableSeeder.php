<?php

use Illuminate\Database\Seeder;

use App\Models\Users\Admin;
use App\Models\Roles\Role;
use App\Models\Permissions\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            ['name' => 'Super Admin'],
            ['name' => 'Admin'],
        ];

        foreach ($roles as $key => $role) {
            $role = Role::updateOrCreate($role);
            if($key > 2) {
                $role->syncPermissions(Permission::all());   
            } else {
                $arr = [];
                $role->syncPermissions($arr);  
            }
        }

        $admin = Admin::first();

        $admin->assignRole(Role::first());
    }
}
