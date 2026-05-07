<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorCampaign;
use App\Models\User;
use App\Models\Doctors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function verifyAdmin(Request $request){
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        // Ensure the user is an admin (role_id = 1)
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => 1,
        ];
        if (Auth::attempt($credentials)) {
            return redirect()->route('admin.blink.dashboard');
        } else {
            return redirect()->route('admin.login')->withErrors(['email' => 'Invalid credentials']);
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('admin.login');
    }

    public function showChangePasswordForm(){
        return view('admin.change_password');
    }

    public function updatePassword(Request $request){
        $request->validate([
            'new_password' => 'required|string|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'The password confirmation does not match.'
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password successfully updated.');
    }

    public function dashboard(){
        return view('admin.dashboard');
    }

    public function show()
    {
        $doctors = DB::table('doctors')->select('doctors.*', 'users.name as so_name', 'users.headquarter as headquarter', 'doctors.id as doctor_id')->join('users', 'doctors.soid', '=', 'users.id')->get();
        return view('admin.doctors', compact('doctors'));
    }


  public function index()
{
    $users = Auth::user();
    // Fetch doctors ordered by newest first and paginate 10 per page
    $doctors = Doctors::orderBy('created_at', 'desc')->paginate(10);
    return view('admin.dashboard', compact('doctors'));
}
public function searchDoctors(Request $request)
{
    $q = trim($request->query('query', ''));

    $doctors = Doctors::when($q, function ($query, $q) {
        $query->where('name', 'like', "%{$q}%")
              ->orWhere('specialty', 'like', "%{$q}%");
    })->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

    $html = '';
    foreach ($doctors as $doctor) {
        $videoExists = !empty($doctor->upload_video);
        $imageExists = !empty($doctor->upload_image);

        $html .= '<tr>';
        $html .= '<td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <img class="h-10 w-10 rounded-full object-cover" src="' . ($doctor->profile_image ?? 'https://placehold.co/100x100/E2E8F0/4A5568?text=Dr') . '" alt="' . e($doctor->name) . '">
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">' . e($doctor->name) . '</div>
                        </div>
                    </div>
                  </td>';
        $html .= '<td class="px-6 py-4 whitespace-nowrap text-start"><div class="text-sm text-gray-900">' . e($doctor->specialty) . '</div></td>';

        // Video
        $html .= '<td class="px-6 py-4 whitespace-nowrap text-start"><div class="flex items-center space-x-2">
                    <form action="' . route('doctor.uploadVideo', $doctor->id) . '" method="POST" enctype="multipart/form-data">' . csrf_field() . '
                        <label class="cursor-pointer inline-block px-3 py-1 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 transition">
                            Upload<input type="file" name="video" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>';
        $html .= $videoExists ? '<a href="' . e($doctor->upload_video) . '" target="_blank" class="inline-block px-3 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition">View</a>' : '<span class="text-gray-400 text-sm">No video</span>';
        $html .= '</div></td>';

        // Image
        $html .= '<td class="px-6 py-4 whitespace-nowrap text-start"><div class="flex items-center space-x-2">
                    <form action="' . route('doctor.uploadImage', $doctor->id) . '" method="POST" enctype="multipart/form-data">' . csrf_field() . '
                        <label class="cursor-pointer inline-block px-3 py-1 bg-purple-500 text-white text-sm rounded-md hover:bg-purple-600 transition">
                            Upload<input type="file" name="image" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>';
        $html .= $imageExists ? '<a href="' . e($doctor->upload_image) . '" target="_blank" class="inline-block px-3 py-1 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 transition">View</a>' : '<span class="text-gray-400 text-sm">No image</span>';
        $html .= '</div></td>';

        $html .= '</tr>';
    }

    // Pagination HTML
    $pagination = '';
    if ($doctors->lastPage() > 1) {
        $current = $doctors->currentPage();
        $last = $doctors->lastPage();
        $start = max(1, $current - 2);
        $end = min($last, $current + 2);

        $pagination .= '<div class="mt-4 flex justify-center space-x-1">';
        $pagination .= $doctors->onFirstPage() 
            ? '<span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Prev</span>'
            : '<a href="' . $doctors->previousPageUrl() . '" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100">Prev</a>';

        if ($start > 1) {
            $pagination .= '<a href="' . $doctors->url(1) . '" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100">1</a>';
            if ($start > 2) $pagination .= '<span class="px-2 py-1 text-gray-500">...</span>';
        }

        for ($page = $start; $page <= $end; $page++) {
            if ($page == $current)
                $pagination .= '<span class="px-3 py-1 bg-blue-500 text-white rounded">' . $page . '</span>';
            else
                $pagination .= '<a href="' . $doctors->url($page) . '" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100">' . $page . '</a>';
        }

        if ($end < $last) {
            if ($end < $last - 1) $pagination .= '<span class="px-2 py-1 text-gray-500">...</span>';
            $pagination .= '<a href="' . $doctors->url($last) . '" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100">' . $last . '</a>';
        }

        $pagination .= $doctors->hasMorePages() 
            ? '<a href="' . $doctors->nextPageUrl() . '" class="px-3 py-1 bg-white border border-gray-300 text-gray-700 rounded hover:bg-gray-100">Next</a>'
            : '<span class="px-3 py-1 bg-gray-200 text-gray-500 rounded cursor-not-allowed">Next</span>';

        $pagination .= '</div>';
    }

    return response()->json([
        'html' => $html,
        'pagination' => $pagination
    ]);
}

public function exportCsv()
{
    $doctors = Doctors::all(['name', 'specialty', 'upload_video', 'upload_image', 'profile_image']);

    $filename = 'doctors_urls_' . date('Y-m-d_H-i-s') . '.csv';
    $handle = fopen('php://memory', 'w');

    // CSV header – use Laravel translation helper
    fputcsv($handle, [
        __('Name'),
        __('Specialty'),
        __('Profile Image URL'),
        __('Video URL'),
        __('Image URL'),
        __('Frequency'),
        __('Intensity')
    ]);

    foreach ($doctors as $doc) {
        fputcsv($handle, [
            $doc->name,
            $doc->specialty,
            $doc->profile_image ?? '',
            $doc->upload_video ?? '',
            $doc->upload_image ?? ''
        ]);
    }

    rewind($handle);

    return Response::streamDownload(function () use ($handle) {
        fpassthru($handle);
    }, $filename, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$filename\"",
    ]);
}

public function uploadVideo(Request $request, Doctors $doctor)
{
    // Validate file with 25MB max
    $request->validate([
        'video' => 'required|mimes:mp4,mov,avi|max:25600', // 25 MB
    ]);

    if ($request->hasFile('video')) {

        // Delete old video if exists
        if ($doctor->upload_video) {
            $filePath = parse_url($doctor->upload_video, PHP_URL_PATH);
            $filePath = ltrim($filePath, '/'); // remove leading slash
            Storage::disk('s3')->delete($filePath);
        }

        // Upload new video using custom storeFile method
        $url = $this->storeFileVideo($request->file('video'));
        $doctor->upload_video = $url;
        $doctor->save();
    }

    // Redirect back with success message
    return redirect()->back()->with('success', 'Video uploaded successfully!');
}
/**
 * Store file to S3 and return public URL
 */
private function storeFileVideo($file)
{
    $name = $file->getClientOriginalName();
    $name = pathinfo($name, PATHINFO_FILENAME);
    $name = str_replace(' ', '_', $name);

    $newFileName = uniqid() . '-' . $name . '.' . $file->extension();
    $filePath = 'doctor_videos/' . $newFileName;

    // store file
    Storage::disk('s3')->put($filePath, file_get_contents($file));

    $bucket_name = env('AWS_BUCKET');
    $region = env('AWS_DEFAULT_REGION');

    $url = 'https://' . $bucket_name . '.s3.' . $region . '.amazonaws.com/' . $filePath;

    return $url;
}

public function uploadImage(Request $request, Doctors $doctor)
    {
        // Validate file (max 25MB)
        $request->validate([
            'image' => 'required|mimes:jpg,jpeg,png,gif|max:25600', // 25MB
        ]);

        if ($request->hasFile('image')) {

            // Delete old image if exists
            if ($doctor->upload_image) {
                $filePath = parse_url($doctor->upload_image, PHP_URL_PATH);
                $filePath = ltrim($filePath, '/'); // remove leading slash
                Storage::disk('s3')->delete($filePath);
            }

            // Upload new image using custom storeFile method
            $url = $this->storeFileImg($request->file('image'));
            $doctor->upload_image = $url;
            $doctor->save();
        }

        // Redirect back with success message
        return redirect()->back()->with('success', 'Image uploaded successfully!');
    }

    /**
     * Store file to S3 and return public URL
     */
    private function storeFileImg($file)
    {
        $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $name = str_replace(' ', '_', $name);
        $newFileName = uniqid() . '-' . $name . '.' . $file->extension();
        $filePath = 'doctor_uploads_image/' . $newFileName;

        // Store file in S3
        Storage::disk('s3')->put($filePath, file_get_contents($file));

        $bucket_name = env('AWS_BUCKET');
        $region = env('AWS_DEFAULT_REGION');

        return 'https://' . $bucket_name . '.s3.' . $region . '.amazonaws.com/' . $filePath;
    }
}
