<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

class DoctorPosterController extends Controller
{
    /**
     * Validate employee login.
     */
    public function login(Request $request)
    {
        $code = $request->input('emp_code');
        $password = $request->input('password');
        
        $employee = Employee::where('emp_code', $code)->first();

        if ($employee && Hash::check($password, $employee->password)) {
            return response()->json([
                'success' => true, 
                'employee' => $employee
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid Employee Code or Password'], 401);
    }
}
