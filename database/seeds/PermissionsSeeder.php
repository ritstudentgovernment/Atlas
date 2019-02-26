<?php

use App\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        $admin = Role::create(['name' => 'admin']);
        $reviewer = Role::create(['name' => 'reviewer']);

        // Define Permissions
        $approve_spots = Permission::create(['name' => 'approve spots']);
        $make_designated_spots = Permission::create(['name' => 'make designated spots']);
        $view_unapproved_spots = Permission::create(['name' => 'view unapproved spots']);
        $mass_upload_spots = Permission::create(['name' => 'mass upload spots']);
        $edit_categories = Permission::create(['name' => 'edit categories']);
        $administer = Permission::create(['name' => 'administer']);

        // Relate permissions to roles
        $admin->syncPermissions([
            $mass_upload_spots,
            $edit_categories,
            $administer,
        ]);
        $reviewer->syncPermissions([
            $make_designated_spots,
            $view_unapproved_spots,
            $approve_spots,
        ]);

        // If the seeds were run in a dev environment, the test admin user will exist. Give them admin rights.
        if ($tempAdminUser = User::where('email', 'scooper@samltest.id')->first()) {
            $tempAdminUser->assignRole(['admin', 'reviewer']);

            // For seeding purposes some users able to create designated spots
            User::all()->each(function (User $user) {
                if (mt_rand(0, 2) == 0) {
                    $user->givePermissionTo('make designated spots');
                }
            });
        }
    }
}
