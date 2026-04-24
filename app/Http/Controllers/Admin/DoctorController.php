<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignAnalytics;
use App\Models\DoctorCampaign;
use App\Models\Doctors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DoctorController extends Controller
{


    public function updateMeta(Request $request, $id)
{
    $request->validate([
        'meta_ad_id' => 'nullable|string|max:255',
    ]);

    $doctor = Doctors::findOrFail($id);
    $doctor->ad_ids = $request->meta_ad_id;
    $doctor->save();

    return back()->with('success', 'Meta Ad ID updated successfully.');
}

    public function doctorCampaign()
    {
        // $campaign = DB::table('doctor_campaign')
        //     ->join('doctors', 'doctor_campaign.doctors_id', '=', 'doctors.id')
        //     ->join('users', 'doctor_campaign.so_id', '=', 'users.id')
        //     ->select(
        //         'doctors.name',
        //         'doctors.specialty',
        //         'doctors.profile_image',
        //         'doctors.clinic_name',
        //         'doctors.clinic_logo',
        //         'doctors.access_grant',
        //         'doctor_campaign.start_date',
        //         'doctor_campaign.end_date',
        //         'doctor_campaign.campaign_status',
        //         'doctor_campaign.id as camp_id',
        //         'doctor_campaign.camp_post',
        //         'users.name as so_name',
        //         'users.emp_no as emp_no',
        //         'doctor_campaign.doctors_id',
        //         'users.headquarter as headquarter'
        //     )
        //     ->get();


            $campaign = DB::table('doctor_campaign')
            ->join('doctors', 'doctor_campaign.doctors_id', '=', 'doctors.id')
            ->join('users', 'doctor_campaign.so_id', '=', 'users.id') // Joining SO details
           ->leftJoin('user_hirarchy_map', 'users.id', '=', 'user_hirarchy_map.sales_teri_off_id') // Mapping SO with hierarchy
            ->leftJoin('users as dsm', 'user_hirarchy_map.div_sales_mngr_id', '=', 'dsm.id') // Divisional Sales Manager
            ->leftJoin('users as rsm', 'user_hirarchy_map.regional_sales_mngr_id', '=', 'rsm.id') // Regional Sales Manager
            ->leftJoin('users as sm', 'user_hirarchy_map.dist_area_sales_id', '=', 'sm.id') // Sales Manager (DM)
            ->leftJoin('campaign_type', 'doctor_campaign.camp_type_id', '=', 'campaign_type.id') // Join with campaign_type
            ->select(

                        

                // Doctor campaign details
                'doctors.name as doctor_name',
                'doctors.specialty',
                      'doctors.ad_ids',
                'doctors.profile_image',
                'doctors.clinic_name',
                'doctors.clinic_logo',
                'doctors.access_grant',
                'doctor_campaign.start_date',
                'doctor_campaign.end_date',
                'doctor_campaign.campaign_status',
                'doctor_campaign.template',
                'doctor_campaign.preview_image',
                'doctor_campaign.preview_link',
                'doctor_campaign.language',
                'doctor_campaign.id as camp_id',
                'doctor_campaign.camp_post',
                'doctor_campaign.doctors_id',
                'doctor_campaign.start_time',
                'doctor_campaign.end_time',
                'campaign_type.camp_name', 

                // SO details
                'users.name as so_name',
                'users.emp_no as so_emp_no',
                'users.headquarter as so_headquarter',

                // DSM details
                'dsm.name as dsm_name',
                'dsm.emp_no as dsm_emp_no',
                'dsm.headquarter as dsm_headquarter',

                // RSM details
                'rsm.name as rsm_name',
                'rsm.emp_no as rsm_emp_no',
                'rsm.headquarter as rsm_headquarter',

                // SM (DM) details
                'sm.name as sm_name',
                'sm.emp_no as sm_emp_no',
                'sm.headquarter as sm_headquarter'
            )
 ->orderBy('doctors.id', 'desc')
             ->get();
        

                // dd($campaign);
        return view('admin.doctorcampaign', compact('campaign'));
    }

    public function changeCampaignStatus(Request $request, $campaignId)
    {
        $id = DoctorCampaign::where('id', $campaignId)->first()->doctors_id;
        DoctorCampaign::where('id', $campaignId)->update([
            'campaign_status' => $request->camp_status
        ]);
        return redirect()->route('doctor.campaign');
    }

    public function campaignDetails($id)
    {
      
        $doctorCampaign = DoctorCampaign::where('id', $id)->first();
        

     
        $doctor = Doctors::where('id', $doctorCampaign->doctors_id)->first();
         $meta_doctor_ads = DB::table('doctor_ads_status')->where('doctor_id','=',$doctor->id)->first();
        $doctorProfileImage = Doctors::where('id', $doctorCampaign->doctors_id)
                        ->value('profile_image') ?? 'path-to-default-image.jpg';
        $campaignAnalytics = CampaignAnalytics::where('campaign_id', $id)->get();
        $msg_convrsn = CampaignAnalytics::where('campaign_id', $id)->sum('msg_convrsn');

        $latest_msg_convrsn = CampaignAnalytics::where('campaign_id', $id)
        ->latest('msg_convrsn')
        ->first();

        $leads_msg_convrsn = $latest_msg_convrsn->msg_convrsn ?? 0;

        // $hotLeads = CampaignAnalytics::where('campaign_id', $id)->sum('other_clicks');
        $latest_hotleads = CampaignAnalytics::where('campaign_id', $id)
        ->latest('other_clicks')
        ->first();

        $hotLeads = $latest_hotleads->other_clicks ?? 0;
       
        $latestLead = CampaignAnalytics::where('campaign_id', $id)
        ->latest('camp_log_date') // Get the latest record by date
        ->first();
    
        $leads = $latestLead->follows_or_like ?? 0; 

        $followsLike = CampaignAnalytics::where('campaign_id', $id)->sum('follows_or_like');
        // $comments = CampaignAnalytics::where('campaign_id', $id)->sum('comments');

        $latest_comment = CampaignAnalytics::where('campaign_id', $id)
        ->latest('comments')
        ->first();

        $comments = $latest_comment->comments ?? 0; 

        // $messageing = CampaignAnalytics::where('campaign_id', $id)->sum('messageing');

        $latest_messageing = CampaignAnalytics::where('campaign_id', $id)
        ->latest('messageing')
        ->first();

        $messageing = $latest_messageing->messageing ?? 0;

        // $peopleReached = CampaignAnalytics::where('campaign_id', $id)->sum('people_reached');


        $peopleReached = CampaignAnalytics::where('campaign_id', $id)
            ->latest('camp_log_date') // Change 'created_at' to your timestamp column
            ->value('people_reached');


        // $postshare = CampaignAnalytics::where('campaign_id', $id)->sum('post_share');

        $latest_postshare = CampaignAnalytics::where('campaign_id', $id)
        ->latest('post_share')
        ->first();

        
if (!$meta_doctor_ads) {
    // No ad found → just keep history empty
    $history = collect();
} else {
    $history = DB::table('doctor_ads_status_logs')
        ->where('ad_id', $meta_doctor_ads->ad_id)
        ->orderBy('recorded_at')
        ->get(['recorded_at','leads_count','impressions','clicks']);
}


        $postshare = $latest_postshare->post_share ?? 0;

        $campPatients = DB::table('camp_patients')->where('camp_id', $id)->get();
      
        return view('admin.campaignreport', compact('doctor', 'doctorCampaign', 'campaignAnalytics', 'msg_convrsn', 'hotLeads', 'followsLike', 'comments', 'peopleReached', 'campPatients', 'doctorProfileImage', 'messageing', 'postshare','leads','leads_msg_convrsn','meta_doctor_ads','history'));
    }

    public function uploadUserCsv(Request $request){
        dd($request->all());
    }

    public function storePreviewDetails(Request $request, $campaign_id)
    {
        try {
            Log::info('Starting storePreviewDetails method', ['campaign_id' => $campaign_id]);
    
            $previewImagePath = null;
    
            // Check if the preview image is present in the request
            if ($request->hasFile('preview_image')) {
                Log::info('Preview image file detected, attempting to store file.');
    
                $file = $request->file('preview_image');
                // Save the file and retrieve the path
                $previewImagePath = $this->storeFile($file); // Ensure storeFile handles file uploads properly
    
                Log::info('Preview image stored successfully.', ['previewImagePath' => $previewImagePath]);
            } else {
                Log::info('No preview image file provided in the request.');
            }
    
            // Prepare data for insertion or update
            $data = [
                'preview_image' => $previewImagePath,
                'preview_link' => $request->input('preview_link'),
                'campaign_status' => 'active', // Update campaign_status to 'active'
            ];
    
            Log::info('Data prepared for database operation', $data);
    
            // Find and update or insert the campaign
            $existingCampaign = DB::table('doctor_campaign')->where('id', $campaign_id)->first();
    
            if ($existingCampaign) {
                Log::info('Existing campaign found, updating campaign.', ['campaign_id' => $campaign_id]);
                DB::table('doctor_campaign')->where('id', $campaign_id)->update(array_filter($data));
                Log::info('Campaign updated successfully.');
            } else {
                Log::info('No existing campaign found, inserting new campaign.', ['campaign_id' => $campaign_id]);
                $data['id'] = $campaign_id;  // Ensure the campaign_id is included
                DB::table('doctor_campaign')->insert(array_filter($data));
                Log::info('New campaign inserted successfully.');
            }
    
            return redirect()->back()->with('success', 'Preview details saved successfully!');
        } catch (\Exception $e) {
            Log::error('Error occurred in storePreviewDetails', [
                'error' => $e->getMessage(),
                'campaign_id' => $campaign_id,
            ]);
    
            return redirect()->back()->withErrors('An error occurred while saving preview details. Please try again.');
        }
    }
    
    public function storeFile($image)
{
    if (!$image instanceof \Illuminate\Http\UploadedFile) {
        throw new \Exception('Invalid file input. Expected an uploaded file.');
    }

    // Generate a unique file name
    $slug = uniqid();
    $fileName = $slug . '.' . $image->getClientOriginalExtension();

    // Define the storage path
    $filePath = 'ajanta/profile/' . $fileName;

    // Save the file to S3
    Storage::disk('s3')->put($filePath, file_get_contents($image));

    // Generate the public URL
    $bucketName = env('AWS_BUCKET');
    $region = env('AWS_DEFAULT_REGION', 'ap-south-1');
    $fileUrl = 'https://' . $bucketName . '.s3.' . $region . '.amazonaws.com/' . $filePath;

    return $fileUrl;
}

    
    private function getFileName($image, $namePrefix)
    {
        $result = [];
    
        // Validate base64 image format
        if (preg_match('/^data:image\/(\w+);base64,/', $image, $matches)) {
            list($type, $file) = explode(';', $image);
            list(, $extension) = explode('/', $type);
            list(, $file) = explode(',', $file);
    
            $result['name'] = $namePrefix . '.' . $extension;
            $result['file'] = $file;
        } else {
            $result['error'] = 'Invalid image format. Ensure the image is in base64 format with a valid MIME type.';
        }
    
        return $result;
    }
    
    

}
