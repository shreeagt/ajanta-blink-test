<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlinkTest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DoctorPosterAdminController extends Controller
{
    public function dashboard()
    {
        $posters = BlinkTest::with(['employee', 'cvs'])->orderBy('created_at', 'desc')->get();
        $pledgeCount = BlinkTest::count();
        $todayCount = BlinkTest::whereDate('created_at', today())->count();
        $soCount = Employee::count();

        return view('admin.blink_test_dashboard', compact('posters', 'pledgeCount', 'todayCount', 'soCount'));
    }

    public function manpower()
    {
        $employees = Employee::orderBy('name', 'asc')->get();
        return view('admin.manpower_master', compact('employees'));
    }

    public function stats()
    {
        $count = BlinkTest::count();
        return response()->json([
            'success' => true,
            'count' => $count,
            'base' => 5240,
            'total' => 5240 + $count
        ]);
    }

    public function importEmployees()
    {
        $filePath = base_path('Dental Emp Details.csv');
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'CSV file not found at ' . $filePath);
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Skip header

        $imported = 0;
        $updated = 0;

        while (($row = fgetcsv($file)) !== FALSE) {
            // Mapping: Sr No., Empno., Employee Name, DIV, Designation, Head Quarter, Mobile No., Email ID, DOJ
            // Indices: 0, 1, 2, 3, 4, 5, 6, 7, 8
            
            if (isset($row[1]) && !empty($row[1])) {
                $empCode = trim($row[1]);
                $name = trim($row[2]);
                $hq = trim($row[5]);

                $employee = Employee::updateOrCreate(
                    ['emp_code' => $empCode],
                    ['name' => $name, 'hq' => $hq]
                );

                if ($employee->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $updated++;
                }
            }
        }

        fclose($file);

        return back()->with('success', "Import completed. Imported: $imported, Updated: $updated");
    }
}
