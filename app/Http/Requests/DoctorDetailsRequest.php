<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DoctorDetailsRequest extends FormRequest
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
            'name' => 'required',
            'profileimage' => 'required',
            'address' => 'required',
            'pincode' => 'required',
            'location' => 'required',
            'instagramurl' => 'required',
            'facebookurl' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'profileimage.required' => 'The profile image is required.',
            'profileimage.image' => 'The file must be an image.',
            'profileimage.mimes' => 'Supported image formats are jpeg, png, jpg, and gif.',
            'profileimage.max' => 'The file size must not exceed 2048 kilobytes.',
        ];
    }
}
