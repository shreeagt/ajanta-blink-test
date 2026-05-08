<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doctor;

class DoctorController extends Controller
{
    public function index(Request $request)
    {
        $soId = $request->query('so_id');
        if (!$soId) {
            return response()->json(['success' => false, 'message' => 'SO ID required'], 400);
        }

        $doctors = Doctor::where('emp_code', $soId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'doctors' => $doctors
        ]);
    }

    public function store(Request $request)
    {
        \Log::info('Saving Doctor Request:', $request->all());
        $request->validate([
            'emp_code' => 'required|string',
            'name' => 'required|string',
            'speciality' => 'nullable|string',
            'mobile' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        $doctor = Doctor::create([
            'emp_code' => $request->emp_code,
            'name' => $request->name,
            'speciality' => $request->speciality,
            'mobile' => $request->mobile,
            'city' => $request->city,
        ]);

        return response()->json([
            'success' => true,
            'doctor' => $doctor
        ]);
    }
}
