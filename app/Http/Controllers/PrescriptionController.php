<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PrescriptionController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_name' => 'required|string|max:255',
            'mobile_number' => 'nullable|string|max:20',
            'clinic_name' => 'nullable|string|max:255',
            'speciality' => 'nullable|string|max:255',
            'prescription_count' => 'required|integer|min:1',
            'visit_date' => 'nullable|date',
            'so_id' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $imageUrls = [];
        if ($request->hasFile('images')) {
            $disk = config('filesystems.default');
            \Log::info("Saving images to disk: " . $disk);
            foreach ($request->file('images') as $image) {
                $path = $image->store('prescriptions', $disk);
                $url = Storage::disk($disk)->url($path);
                $imageUrls[] = $url;
                \Log::info("Saved image to path: $path, generated URL: $url");
            }
        } else {
            \Log::warning("No images found in the request for doctor: " . $request->doctor_name);
        }

        $prescription = Prescription::create([
            'so_id' => $request->so_id,
            'doctor_name' => $request->doctor_name,
            'mobile_number' => $request->mobile_number,
            'clinic_name' => $request->clinic_name,
            'speciality' => $request->speciality,
            'prescription_count' => $request->prescription_count,
            'visit_date' => $request->visit_date,
            'images' => $imageUrls
        ]);

        return response()->json([
            'success' => true,
            'data' => $prescription
        ]);
    }

    public function dashboardStats(Request $request)
    {
        $soId = $request->query('so_id');
        if (!$soId) {
            return response()->json(['error' => 'so_id required'], 400);
        }

        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();

        $todayCount = Prescription::where('so_id', $soId)
            ->where(function($q) use ($today) {
                $q->whereDate('visit_date', $today)
                  ->orWhere(function($sq) use ($today) {
                      $sq->whereNull('visit_date')->whereDate('created_at', $today);
                  });
            })
            ->sum('prescription_count');

        $monthCount = Prescription::where('so_id', $soId)
            ->where(function($q) use ($startOfMonth) {
                $q->where('visit_date', '>=', $startOfMonth)
                  ->orWhere(function($sq) use ($startOfMonth) {
                      $sq->whereNull('visit_date')->where('created_at', '>=', $startOfMonth);
                  });
            })
            ->sum('prescription_count');

        $offset = $request->query('offset', 0);
        $limit = 10;

        $historyQuery = Prescription::where('so_id', $soId)
            ->orderBy('id', 'desc');

        $totalHistory = (clone $historyQuery)->count();
        $history = $historyQuery->skip($offset)->take($limit)->get();

        return response()->json([
            'success' => true,
            'today_count' => (int)$todayCount,
            'month_count' => (int)$monthCount,
            'total_history_count' => $totalHistory,
            'history' => $history
        ]);
    }
}
