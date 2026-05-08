<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

class ImportEmployeeCsvData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $soPath = base_path('SO_employe.csv');
        $dmPath = base_path('DM_employee.csv');
        
        // Import SO Employees
        if (file_exists($soPath)) {
            $file = fopen($soPath, 'r');
            fgetcsv($file); // Skip header
            while (($row = fgetcsv($file)) !== FALSE) {
                if (isset($row[0]) && !empty($row[0])) {
                    $empCode = trim(preg_replace('/\s+/', '', $row[0])); 
                    $name = trim($row[1] ?? '');
                    $hq = trim($row[2] ?? '');
                    $dmName = trim($row[3] ?? '');
                    $rsmName = trim($row[4] ?? '');
                    $state = trim($row[5] ?? '');

                    Employee::updateOrCreate(
                        ['emp_code' => $empCode],
                        [
                            'name' => $name, 
                            'hq' => $hq,
                            'dm_name' => $dmName,
                            'rsm_name' => $rsmName,
                            'state' => $state,
                            'role' => 'SO',
                            'password' => Hash::make('Ajanta@123')
                        ]
                    );
                }
            }
            fclose($file);
        }

        // Import DM Employees
        if (file_exists($dmPath)) {
            $file = fopen($dmPath, 'r');
            fgetcsv($file); // Skip header
            while (($row = fgetcsv($file)) !== FALSE) {
                if (isset($row[1]) && !empty($row[1])) {
                    $empCode = trim(preg_replace('/\s+/', '', $row[1])); 
                    $name = trim($row[2] ?? '');
                    $rsmName = trim($row[3] ?? '');
                    $state = trim($row[4] ?? '');

                    Employee::updateOrCreate(
                        ['emp_code' => $empCode],
                        [
                            'name' => $name, 
                            'hq' => $state, 
                            'dm_name' => null,
                            'rsm_name' => $rsmName,
                            'state' => $state,
                            'role' => 'DM',
                            'password' => Hash::make('Ajanta@123')
                        ]
                    );
                }
            }
            fclose($file);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No reversible action needed for data import
    }
}
