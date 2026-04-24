<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // First, check if admin already exists
        $admin = User::where('email', 'admin@ajantapharma.com')->first();
        
        if (!$admin) {
            User::create([
                'name' => 'Super Admin',
                'emp_no' => 'ADMIN001',
                'email' => 'admin@ajantapharma.com',
                'password' => Hash::make('Admin@123'),
                'role_id' => 1,
                'role' => 'Admin'
            ]);
            $this->command->info('Admin user created successfully. Use Admin Id: ADMIN001 to login.');
        } else {
            $admin->update([
                'password' => Hash::make('Admin@123'),
                'emp_no' => 'ADMIN001',
                'role_id' => 1,
                'role' => 'Admin'
            ]);
            $this->command->info('Admin user updated successfully. Use Admin Id: ADMIN001 to login.');
        }

        $this->call(EmployeeSeeder::class);
    }
}
