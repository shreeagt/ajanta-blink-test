<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlinkTest;
use Carbon\Carbon;

class BlinkTestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'emp_code' => 'required|string',
            'blink_count' => 'required|integer',
        ]);

        $test = BlinkTest::create([
            'emp_code' => $request->emp_code,
            'blink_count' => $request->blink_count,
        ]);

        return response()->json(['success' => true, 'test' => $test]);
    }

    public function dashboardStats(Request $request)
    {
        $soId = $request->query('so_id');
        $offset = max(0, (int)$request->query('offset', 0));
        
        $todayTests = BlinkTest::where('emp_code', $soId)
            ->whereDate('created_at', Carbon::today())
            ->count();
            
        $monthlyTests = BlinkTest::where('emp_code', $soId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $totalTests = BlinkTest::where('emp_code', $soId)->count();
        
        $history = BlinkTest::where('emp_code', $soId)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'today' => $todayTests,
            'month' => $monthlyTests,
            'total' => $totalTests,
            'history' => $history
        ]);
    }
}
