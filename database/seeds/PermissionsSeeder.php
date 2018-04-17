<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $admin    = Role::create(['name' => 'admin']);
        $reviewer = Role::create(['name' => 'reviewer']);
        $user     = Role::create(['name' => 'user']);

        $add_spot               = Permission::create(['name' => 'add spot']);
        $approve_spots          = Permission::create(['name' => 'approve spots']);
        $view_unapproved_spots  = Permission::create(['name' => 'view unapproved spots']);
        $mass_upload_spots      = Permission::create(['name' => 'mass upload spots']);
        $edit_categories        = Permission::create(['name' => 'edit categories']);
        $administer             = Permission::create(['name' => 'administer']);

        $admin->syncPermissions([
            $mass_upload_spots,
            $edit_categories,
            $administer
        ]);

        $reviewer->syncPermissions([
            $view_unapproved_spots,
            $approve_spots
        ]);

        $user->givePermissionTo($add_spot);

    }
}
