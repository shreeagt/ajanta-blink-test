<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CampaignAnalytics;
use Illuminate\Http\Request;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;

class DoctorCampaignController extends Controller
{

    public function addAnalytics($id)
    {
        return view('admin.campaign.addlist', compact('id'));
    }

    // public function storeAnalytics(Request $request)
    // {
    //     $request->validate([
    //         'campaign_log_date' => 'required|date',
    //         'people_reached' => 'required|integer|min:0',
    //         'messaging_conversion' => 'nullable|integer|min:0',
    //         'post_follow_like' => 'nullable|integer|min:0',
    //         'post_comment' => 'required',
    //         'post_other_click' => 'nullable|integer|min:0',
    //         'post_share' => 'required',
    //         'messageing' => 'required'
    //     ]);

    //     $data = [
    //         'campaign_id' => $request->campaign_id,
    //         'camp_log_date' => $request->campaign_log_date,
    //         'msg_convrsn' => $request->messaging_conversion,
    //         'follows_or_like' => $request->post_follow_like,
    //         'people_reached' => $request->people_reached,
    //         'comments' => $request->post_comment,
    //         'other_clicks' => $request->post_other_click,
    //         'post_share' => $request->post_share,
    //         'messageing' => $request->messageing
    //     ];

    //     CampaignAnalytics::create($data);
    //     return redirect()->route('doctro.campaign.details', $request->campaign_id);
    // }
    public function storeAnalytics(Request $request)
{
    $request->validate([
        'campaign_log_date' => 'required|date',
        'people_reached' => 'required|integer|min:0',
        'messaging_conversion' => 'nullable|integer|min:0',
        'post_follow_like' => 'nullable|integer|min:0',
        'post_comment' => 'required|string',
        'post_other_click' => 'nullable|integer|min:0',
        'post_share' => 'required|integer|min:0',
        'messageing' => 'required|integer|min:0'
    ]);

    $data = [
        'campaign_id' => $request->campaign_id,
        'camp_log_date' => $request->campaign_log_date,
        'msg_convrsn' => $request->messaging_conversion,
        'follows_or_like' => $request->post_follow_like,
        'people_reached' => $request->people_reached,
        'comments' => $request->post_comment,
        'other_clicks' => $request->post_other_click,
        'post_share' => $request->post_share,
        'messageing' => $request->messageing
    ];

    CampaignAnalytics::create($data);
    return redirect()->route('doctro.campaign.details', $request->campaign_id);
}

public function showDoctorCampaigns()
{
    // $currentYear = now()->year;
    // $currentMonth = now()->month;

$campaign = DB::table('doctor_campaign')
    ->join('doctors', 'doctor_campaign.doctors_id', '=', 'doctors.id')
    ->join('users', 'doctor_campaign.so_id', '=', 'users.id') // SO details
    ->join('user_hirarchy_map', 'users.id', '=', 'user_hirarchy_map.sales_teri_off_id')
    ->leftJoin('users as dsm', 'user_hirarchy_map.div_sales_mngr_id', '=', 'dsm.id') // DSM
    ->leftJoin('users as rsm', 'user_hirarchy_map.regional_sales_mngr_id', '=', 'rsm.id') // RSM
    ->leftJoin('users as dm', 'user_hirarchy_map.dist_area_sales_id', '=', 'dm.id') // DM
    ->join('campaign_type', 'doctor_campaign.camp_type_id', '=', 'campaign_type.id')
    ->join('campaign_analytics', 'doctor_campaign.id', '=', 'campaign_analytics.campaign_id')
    ->select(
        'doctors.name as doctor_name',
        'doctors.specialty',
        'doctors.profile_image',
        'doctors.clinic_name',
        'doctors.clinic_logo',
        'doctors.access_grant',
        'doctor_campaign.start_date',
        'doctor_campaign.end_date',
        'doctor_campaign.campaign_status',
        'doctor_campaign.id as camp_id',
        'doctor_campaign.camp_post',
        'doctor_campaign.doctors_id',
        'users.name as so_name',
        'users.emp_no as so_emp_no',
        'users.headquarter as so_headquarter',
        'dsm.name as dsm_name',
        'dsm.emp_no as dsm_emp_no',
        'dsm.headquarter as dsm_headquarter',
        'rsm.name as rsm_name',
        'rsm.emp_no as rsm_emp_no',
        'rsm.headquarter as rsm_headquarter',
        'dm.name as dm_name',
        'dm.emp_no as dm_emp_no',
        'dm.headquarter as dm_headquarter',
        'campaign_type.camp_name as campaign_name',
        DB::raw('SUM(campaign_analytics.msg_convrsn) as Post_Reaction'),
        DB::raw('SUM(campaign_analytics.follows_or_like) as Leads'),
        DB::raw('SUM(campaign_analytics.comments) as mobile_feeds'),
        DB::raw('SUM(campaign_analytics.other_clicks) as facebook_reels'),
        DB::raw('SUM(campaign_analytics.people_reached) as reach'),
        DB::raw('SUM(campaign_analytics.post_share) as facebook_feeds'),
        DB::raw('SUM(campaign_analytics.messageing) as Suggested_feed')
    )
    // ->whereMonth('campaign_analytics.camp_log_date', $currentMonth)
    // ->whereYear('campaign_analytics.camp_log_date', $currentYear)
    ->groupBy(
        'doctor_campaign.id',
        'doctors.name',
        'doctors.specialty',
        'doctors.profile_image',
        'doctors.clinic_name',
        'doctors.clinic_logo',
        'doctors.access_grant',
        'doctor_campaign.start_date',
        'doctor_campaign.end_date',
        'doctor_campaign.campaign_status',
        'doctor_campaign.camp_post',
        'doctor_campaign.doctors_id',
        'users.name',
        'users.emp_no',
        'users.headquarter',
        'dsm.name',
        'dsm.emp_no',
        'dsm.headquarter',
        'rsm.name',
        'rsm.emp_no',
        'rsm.headquarter',
        'dm.name',
        'dm.emp_no',
        'dm.headquarter',
        'campaign_type.camp_name'
    )
    ->orderBy('doctor_campaign.start_date')
    ->get();


    return view('admin.doctorcampaignreport', compact('campaign'));
}


    public function editAnalytics($campId, $logId)
    {
        $campLog = CampaignAnalytics::find($logId);
        return view('admin.campaign.editlist', compact('campId', 'logId', 'campLog'));
    }

    public function updateAnalytics(Request $request, $campId, $logId)
    {
        $request->validate([
            'campaign_log_date' => 'required|date',
            'people_reached' => 'required|integer|min:0',
            'messaging_conversion' => 'nullable|integer|min:0',
            'post_follow_like' => 'nullable|integer|min:0',
            'post_comment' => 'required',
            'post_other_click' => 'nullable|integer|min:0',
        ]);
        $data = [
            'campaign_id' => $request->campaign_id,
            'camp_log_date' => $request->campaign_log_date,
            'msg_convrsn' => $request->messaging_conversion,
            'follows_or_like' => $request->post_follow_like,
            'people_reached' => $request->people_reached,
            'comments' => $request->post_comment,
            'other_clicks' => $request->post_other_click
        ];
        CampaignAnalytics::where('id', $logId)->update($data);
        return redirect()->route('doctro.campaign.details', $campId);
    }

    // public function peopleReached(Request $request)
    // {
    //     $analytics = CampaignAnalytics::select('camp_log_date', 'people_reached')->where('campaign_id', $request->camp_id)->get();
    //     return response()->json([
    //         'message' => 'People Reached',
    //         'success' => true,
    //         'data' => $analytics
    //     ]);
    // }

    public function peopleReached(Request $request)
{
    $analytics = CampaignAnalytics::select('camp_log_date', 'people_reached')
        ->where('campaign_id', $request->camp_id)
        ->orderBy('camp_log_date', 'asc') // Ensures the first date comes first
        ->get();

    return response()->json([
        'message' => 'People Reached',
        'success' => true,
        'data' => $analytics
    ]);
}


    


    public function createDoc()
    {

        $campaigns = Campaign::all();
        dd($campaigns);
        return view('campaign.createDoc', compact('campaigns'));
    }


    public function uploadUserCsv(Request $request, $campId, $soId)
    {
        $file = $request->file('user_csv');
        $fileContents = file($file->getPathname());
        $cout = 0;
        $dataCsv = [];
        foreach ($fileContents as $line) {
            $data = str_getcsv($line);
            if($cout != 0){
                $dataCsv[] = [
                    'so_id' => $soId,
                    'camp_id' => $campId,
                    'name' => $data[0],
                    'email' => $data[1],
                    'phone_number' => $data[2],
                    'address' => $data[3]
                ];
            }
            $cout++;
        }
        DB::table('camp_patients')->insert($dataCsv);
        return redirect()->route('doctro.campaign.details', $request->doctor_id);
    }
}
