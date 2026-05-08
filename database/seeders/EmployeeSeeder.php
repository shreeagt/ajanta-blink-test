<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        $employees = [
            [
                'emp_code' => 'TEST001',
                'name'     => 'Ravi Sharma',
                'hq'       => 'Mumbai',
                'password' => Hash::make('Ajanta'),
            ],
            [
                'emp_code' => 'TEST002',
                'name'     => 'Priya Patel',
                'hq'       => 'Pune',
                'password' => Hash::make('Ajanta'),
            ],
            [
                'emp_code' => 'TEST003',
                'name'     => 'Amit Verma',
                'hq'       => 'Ahmedabad',
                'password' => Hash::make('Ajanta'),
            ],
        ];

        foreach ($employees as $emp) {
            Employee::updateOrCreate(['emp_code' => $emp['emp_code']], $emp);
        }
    }
}
