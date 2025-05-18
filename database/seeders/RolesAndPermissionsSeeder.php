<?php

namespace Database\Seeders;

use App\Models\User; // Add this line
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $teacherRole = Role::create(['name' => 'teacher']);

        // We can define permissions here later if needed
        // Example:
        // Permission::create(['name' => 'edit articles']);
        // Permission::create(['name' => 'delete articles']);

        // $roleAdmin = Role::findByName('admin');
        // $roleAdmin->givePermissionTo('edit articles');
        // $roleAdmin->givePermissionTo('delete articles');

        // Create Admin User
        $adminUser = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            // 'password' => bcrypt('password'), // Handled by factory if not specified, or set explicitly
        ]);
        $adminUser->assignRole($adminRole);

        // Create Teacher User
        $teacherUser = User::factory()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
            // 'password' => bcrypt('password'), // Handled by factory if not specified, or set explicitly
        ]);
        $teacherUser->assignRole($teacherRole);
    }
}
