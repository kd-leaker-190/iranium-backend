<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        Role::firstOrCreate(['name' => 'super_admin']);
        User::firstOrCreate([
            'email' => 'super@iraniom.ir',
            'name' => 'مدیر',
            'phone' => '09120000000',
            'password' => Hash::make('password'),
        ]);
        User::where('email', 'super@iraniom.ir')->firstOrFail()->assignRole('super_admin');

        // Team Admin
        $teamAdminPermissions = [
            // Team اصلی
            'view_team',
            'view_any_team',
            'create_team',
            'update_team',
            'restore_team',
            'restore_any_team',
            'replicate_team',
            'reorder_team',
            'delete_team',
            'delete_any_team',
            'force_delete_team',
            'force_delete_any_team',

            // TeamUsers
            'view_team::users',
            'view_any_team::users',
            'create_team::users',
            'update_team::users',
            'restore_team::users',
            'restore_any_team::users',
            'replicate_team::users',
            'reorder_team::users',
            'delete_team::users',
            'delete_any_team::users',
            'force_delete_team::users',
            'force_delete_any_team::users',
        ];
        Role::firstOrCreate(['name' => 'team_admin']);
        Role::where('name', 'team_admin')->firstOrFail()->syncPermissions($teamAdminPermissions);
        User::firstOrCreate([
            'email' => 'team@iraniom.ir',
            'name' => 'مدیر تیم',
            'phone' => '09130000000',
            'password' => Hash::make('password'),
        ]);
        User::where('email', 'team@iraniom.ir')->firstOrFail()->assignRole('team_admin');
    }
}
