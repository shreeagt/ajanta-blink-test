<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateTestEmployeesSeeder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $employees = [
            [
                'emp_code' => 'TEST001',
                'name'     => 'Ravi Sharma',
                'hq'       => 'Mumbai',
                'password' => Hash::make('Gullak@123'),
            ],
            [
                'emp_code' => 'TEST002',
                'name'     => 'Priya Patel',
                'hq'       => 'Pune',
                'password' => Hash::make('Gullak@123'),
            ],
            [
                'emp_code' => 'TEST003',
                'name'     => 'Amit Verma',
                'hq'       => 'Ahmedabad',
                'password' => Hash::make('Gullak@123'),
            ],
        ];

        foreach ($employees as $employee) {
            // Only insert if the emp_code doesn't already exist
            DB::table('employees')->updateOrInsert(
                ['emp_code' => $employee['emp_code']],
                array_merge($employee, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('employees')->whereIn('emp_code', ['TEST001', 'TEST002', 'TEST003'])->delete();
    }
}
