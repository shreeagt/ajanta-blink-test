<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Avoid duplicate creation
        $email = 'admin@ajantapharma.com';
        if (User::where('email', $email)->exists()) {
            return;
        }

        User::create([
            'emp_no'   => 'ADMIN001',
            'email'    => $email,
            'name'     => 'Admin Demo',
            // Assuming the users table has a role_id column where 1 = admin
            'role_id'  => 1,
            'password' => Hash::make('Admin@123'),
        ]);
    }
}
