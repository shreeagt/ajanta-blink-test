<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlinkTest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class DoctorPosterAdminController extends Controller
{
    public function dashboard()
    {
        $posters = BlinkTest::with(['employee', 'cvs', 'doctor'])->orderBy('created_at', 'desc')->get();
        $pledgeCount = $posters->count();
        $todayCount = $posters->where('created_at', '>=', Carbon::today())->count();
        $soCount = Employee::where('role', 'SO')->count();
        $uniqueDMs = Employee::where('role', 'SO')->whereNotNull('dm_name')->distinct()->pluck('dm_name');

        return view('admin.blink_test_dashboard', compact('posters', 'pledgeCount', 'todayCount', 'soCount', 'uniqueDMs'));
    }

    public function manpower()
    {
        $soEmployees = Employee::where('role', 'SO')->orderBy('name', 'asc')->get();
        $dmEmployees = Employee::where('role', 'DM')->orderBy('name', 'asc')->get();
        return view('admin.manpower_master', compact('soEmployees', 'dmEmployees'));
    }

    public function exportManpower()
    {
        $employees = Employee::orderBy('role', 'desc')->orderBy('name', 'asc')->get();
        $filename = "Manpower_Master_" . date('Y-m-d') . ".csv";
        $handle = fopen('php://output', 'w');
        
        // CSV Headers
        fputcsv($handle, ['Role', 'Employee Code', 'Name', 'HQ/State', 'DM Name', 'RSM Name', 'Personalized URL']);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $baseUrl = request()->getSchemeAndHttpHost();

        foreach ($employees as $emp) {
            fputcsv($handle, [
                $emp->role,
                $emp->emp_code,
                $emp->name,
                ($emp->hq ? $emp->hq : '') . ($emp->state ? ' / ' . $emp->state : ''),
                $emp->dm_name ?? '-',
                $emp->rsm_name ?? '-',
                $baseUrl . '/' . $emp->emp_code
            ]);
        }

        fclose($handle);
        exit;
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
        $soPath = base_path('SO_employe.csv');
        $dmPath = base_path('DM_employee.csv');
        
        $imported = 0;
        $updated = 0;

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

                    $employee = Employee::updateOrCreate(
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
                    $employee->wasRecentlyCreated ? $imported++ : $updated++;
                }
            }
            fclose($file);
        }

        // Import DM Employees
        if (file_exists($dmPath)) {
            $file = fopen($dmPath, 'r');
            fgetcsv($file); // Skip header
            while (($row = fgetcsv($file)) !== FALSE) {
                // DM_employee.csv format: ,EMP-CODE,DM NAME,RSM NAME,STATE
                if (isset($row[1]) && !empty($row[1])) {
                    $empCode = trim(preg_replace('/\s+/', '', $row[1])); 
                    $name = trim($row[2] ?? '');
                    $rsmName = trim($row[3] ?? '');
                    $state = trim($row[4] ?? '');

                    $employee = Employee::updateOrCreate(
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
                    $employee->wasRecentlyCreated ? $imported++ : $updated++;
                }
            }
            fclose($file);
        }

        if ($imported == 0 && $updated == 0) {
            return back()->with('error', 'No CSV files found or files were empty.');
        }

        return back()->with('success', "Import completed. Total Imported: $imported, Total Updated: $updated");
    }
}
