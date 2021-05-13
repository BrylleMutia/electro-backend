<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // CREATE ROLES
        $roles = ['buyer', 'seller'];
        foreach($roles as $index => $role) {
            $newRole = Role::create([
                'id' => $index + 1,
                'role' => $role
            ]);

            $newRole->save();
        }
    }
}
