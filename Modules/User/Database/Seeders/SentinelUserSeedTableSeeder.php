<?php

namespace Modules\User\Database\Seeders;

use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class SentinelUserSeedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Create an admin user
        $user = Sentinel::create(
            [
                'email' => 'admin@test.com',
                'password' => 'popopo',
                'first_name' => 'Admin',
                'last_name' => 'Test',
            ]
        );
        // Activate the admin directly
        $activation = Activation::create($user);
        Activation::complete($user, $activation->code);

        // Find the group using the group id
        $group = Sentinel::findRoleBySlug('super-admin');

        // Assign the group to the user
        $group->users()->attach($user);
    }
}
