<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'camp_type' => 'required',
            'date' => 'required',
            'target_area' => 'required',
            'name' => 'required',
            'specialty' => 'required',
            'clinic_name' => 'required',
            'clinic_logo' => 'nullable',
            'address' => 'required',
            // 'city' => 'required',
            // 'state' => 'required',
            'pincode' => 'required',
            'description' => 'nullable',
            'language' => 'required',
            'template'=> 'required'
        ];
    }
}
