<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\User;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Define Roles
        $admin    = Role::create(['name' => 'admin']);
        $reviewer = Role::create(['name' => 'reviewer']);

        // Define Permissions
        $approve_spots          = Permission::create(['name' => 'approve spots']);
        $view_unapproved_spots  = Permission::create(['name' => 'view unapproved spots']);
        $mass_upload_spots      = Permission::create(['name' => 'mass upload spots']);
        $edit_categories        = Permission::create(['name' => 'edit categories']);
        $administer             = Permission::create(['name' => 'administer']);

        // Relate permissions to roles
        $admin->syncPermissions([
            $mass_upload_spots,
            $edit_categories,
            $administer
        ]);
        $reviewer->syncPermissions([
            $view_unapproved_spots,
            $approve_spots
        ]);

        // If the seeds were run in a dev environment, the test admin user will exist. Give them admin rights.
        if($tempAdminUser = User::where('email','admin@rit.edu')->first()){

            $tempAdminUser->assignRole(['admin','reviewer']);

        }

    }
}
